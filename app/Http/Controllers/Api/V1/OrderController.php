<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OrderResource;
use App\Http\Requests\V1\OrderStoreRequest;
use App\Http\Requests\V1\OrderTransitionRequest;
use App\Events\OrderStatusChanged;
use App\Services\Chat;
use App\Services\EzZitouniDealMediator;
use App\Jobs\SendUnreadDealEmail;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\Payments\LocalPaymentAdapter;

class OrderController extends ApiController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);
        $q = Order::query()->with(['buyer','seller','listing'])->latest();
        return $this->paginate($q, OrderResource::class);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return $this->ok(new OrderResource($order->load(['buyer','seller','listing'])));
    }

    public function store(OrderStoreRequest $request)
    {
        $this->authorize('create', Order::class);
        $data = $request->validated();
        $data['total'] = (string) ((float) $data['qty'] * (float) $data['price_unit']);
        $data['status'] = Order::STATUS_PENDING;
        $order = Order::create($data);
        $this->audit('order.created', 'order', $order->id);
        
        // Notify seller via in-app & push
        if ($order->seller) {
            $order->seller->notify(new \App\Notifications\NewDeal($order));
            
            // Queue 5-minute delayed email reminder if still unread/pending
            SendUnreadDealEmail::dispatch($order->id, (int)$order->seller_id)->delay(now()->addMinutes(5));
        }

        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);
        EzZitouniDealMediator::onDealCreated($thread, $order);

        return $this->ok(new OrderResource($order->load(['buyer','seller','listing'])), 201);
    }

    public function counterOffer(Request $request, Order $order)
    {
        $user = $request->user();
        if (!in_array((int)$user->id, [(int)$order->buyer_id, (int)$order->seller_id], true) && $user->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }

        $request->validate([
            'price_unit' => 'required|numeric|min:0.1',
        ]);

        $newPriceUnit = (float) $request->price_unit;
        $order->price_unit = (string) $newPriceUnit;
        $order->total = (string) ((float)$order->qty * $newPriceUnit);
        $order->status = Order::STATUS_PENDING;
        $order->save();

        $recipientId = ((int)$user->id === (int)$order->buyer_id) ? (int)$order->seller_id : (int)$order->buyer_id;
        $recipient = \App\Models\User::find($recipientId);

        if ($recipient) {
            $recipient->notify(new \App\Notifications\NewDeal($order));
            SendUnreadDealEmail::dispatch($order->id, $recipientId)->delay(now()->addMinutes(5));
        }

        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);
        EzZitouniDealMediator::onCounterOffer($thread, $order, $user->name);

        return $this->ok(new OrderResource($order->fresh()->load(['buyer','seller','listing'])));
    }

    public function transition(OrderTransitionRequest $request, Order $order)
    {
        $this->authorize('update', $order);
        $map = [
            'confirm' => Order::STATUS_CONFIRMED,
            'ready' => Order::STATUS_READY,
            'ship' => Order::STATUS_SHIPPING,
            'deliver' => Order::STATUS_DELIVERED,
            'cancel' => Order::STATUS_CANCELLED,
        ];
        $nextVerb = $request->validated()['next'];
        $next = $map[$nextVerb];

        $user = $request->user();
        if (in_array($nextVerb, ['confirm','ready','ship'], true) && (int)$user->id !== (int)$order->seller_id && $user->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }
        if ($nextVerb === 'cancel' && !in_array((int)$user->id, [(int)$order->buyer_id, (int)$order->seller_id], true) && $user->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }

        $from = $order->status;

        // Enforce strict business rules
        if ($nextVerb === 'confirm') {
            $order->total = (string) ((float) $order->qty * (float) $order->price_unit);
            
            // Automatically decrement quantity on the listing when a deal is confirmed
            $listing = $order->listing;
            if ($listing && $listing->quantity !== null) {
                $newQty = (float) $listing->quantity - (float) $order->qty;
                
                if ($newQty < 0) {
                    abort(422, 'الكمية المطلوبة غير متوفرة حالياً.');
                }
                
                $listing->quantity = (string) $newQty;
                
                if ($newQty <= 0) {
                    $listing->status = 'sold';
                }
                
                $listing->save();
            }
        }

        if ($nextVerb === 'ready') {
            $meta = $order->meta ?? [];
            $meta['pack_started_at'] = now()->toISOString();
            $order->meta = $meta;
        }

        if ($nextVerb === 'ship') {
            $load = \App\Models\Load::where('order_id', $order->id)->first();
            if (!$load) {
                $sellerDefault = $order->seller?->addresses()->first();
                $buyerDefault = $order->buyer?->addresses()->first();
                if (!$sellerDefault || !$buyerDefault) {
                    abort(422, 'Missing default addresses.');
                }
                $productType = $order->listing?->product?->type ?? 'oil';
                $load = \App\Models\Load::create([
                    'owner_id' => $order->seller_id,
                    'order_id' => $order->id,
                    'kind' => $productType,
                    'qty' => $order->qty,
                    'unit' => $order->unit,
                    'pickup_addr_id' => $sellerDefault->id,
                    'dropoff_addr_id' => $buyerDefault->id,
                    'price_floor' => $order->price_unit,
                    'price_ceiling' => $order->price_unit,
                    'status' => \App\Models\Load::ST_NEW,
                    'meta' => ['pricing_auto' => true],
                ]);
            }
            $isMatched = $load->status === \App\Models\Load::ST_MATCHED;
            $hasTrip = \App\Models\Trip::whereJsonContains('load_ids', $load->id)->exists();
            if (!$isMatched && !$hasTrip) {
                abort(422, 'Load must be matched or trip created before shipping.');
            }
        }

        if ($nextVerb === 'deliver') {
            $loads = \App\Models\Load::where('order_id', $order->id)->get();
            if ($loads->isEmpty()) {
                abort(422, trans('micro.pod_required'));
            }
            foreach ($loads as $ld) {
                if ($ld->status !== \App\Models\Load::ST_DELIVERED) {
                    abort(422, trans('micro.pod_required'));
                }
                $trip = \App\Models\Trip::whereJsonContains('load_ids', $ld->id)->latest('id')->first();
                $pod = $trip?->pods()->latest('id')->first();
                if (!$pod || !$pod->verified_at) {
                    abort(422, trans('micro.pod_required'));
                }
            }
        }

        if ($nextVerb === 'cancel' && $user->role === 'admin' && $order->status !== Order::STATUS_PENDING) {
            $meta = $order->meta ?? [];
            $meta['late_cancel_penalty'] = true;
            $order->meta = $meta;
        }

        $order->moveTo($next);

        // Post-transition side-effects
        if ($nextVerb === 'deliver') {
            if ($order->payment_method === 'cod' && $order->payment_status !== Order::PAY_COLLECTED) {
                $paymentService = new LocalPaymentAdapter();
                $result = $paymentService->capture([
                    'order_id' => $order->id,
                    'amount' => $order->total,
                    'method' => $order->payment_method,
                ]);
                if ($result['status'] === 'captured') {
                    $order->payment_status = Order::PAY_COLLECTED;
                    $order->save();
                    event(new \App\Events\OrderPaid($order->id, $result['transaction_id']));
                }
            }
        }

        $this->audit('order.transition', 'order', $order->id);
        event(new OrderStatusChanged($order->id, $from, $next));

        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);

        if ($nextVerb === 'confirm') {
            EzZitouniDealMediator::onDealConfirmed($thread, $order);
        } elseif ($nextVerb === 'cancel') {
            EzZitouniDealMediator::onDealRejected($thread, $order, $user->name);
        } elseif ($nextVerb === 'deliver') {
            $load = \App\Models\Load::where('order_id', $order->id)->first();
            if ($load) {
                EzZitouniDealMediator::onDeliveryCompleted($thread, $load);
            }
        }

        return $this->ok(new OrderResource($order->fresh()->load(['buyer','seller','listing'])));
    }
}
