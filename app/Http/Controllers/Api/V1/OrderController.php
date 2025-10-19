<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OrderResource;
use App\Http\Requests\V1\OrderStoreRequest;
use App\Http\Requests\V1\OrderTransitionRequest;
use App\Events\OrderStatusChanged;
use App\Services\Chat;
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
        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);
        Chat::system($thread, 'تم إنشاء طلب جديد.');
        return $this->ok(new OrderResource($order->load(['buyer','seller','listing'])), 201);
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

        // Simple role enforcement: confirm/ready by seller, cancel by buyer/seller (or admin via policy), ship by seller, deliver by carrier/admin (validated via POD in Trip)
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
            // clamp total
            $order->total = (string) ((float) $order->qty * (float) $order->price_unit);
            // TODO: re-check stock/min_order (Prompt2)
        }

        if ($nextVerb === 'ready') {
            // Freeze quantity-related fields (application-level: do not allow edits after ready)
            // Could set a meta flag to indicate packing started
            $meta = $order->meta ?? [];
            $meta['pack_started_at'] = now()->toISOString();
            $order->meta = $meta;
        }

        if ($nextVerb === 'ship') {
            // Load must exist or be created, and be matched or have a trip created
            $load = \App\Models\Load::where('order_id', $order->id)->first();
            if (!$load) {
                // Auto-create load from order
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
                $thread = \App\Services\Chat::ensureThread('load', $load->id, [$order->seller_id]);
                \App\Services\Chat::system($thread, '🔗 تم إنشاء حمولة للطلب #'.$order->id);
            }
            // Only allow shipping when matched or trip exists
            $isMatched = $load->status === \App\Models\Load::ST_MATCHED;
            $hasTrip = \App\Models\Trip::whereJsonContains('load_ids', $load->id)->exists();
            if (!$isMatched && !$hasTrip) {
                abort(422, 'Load must be matched or trip created before shipping.');
            }
        }

        if ($nextVerb === 'deliver') {
            // Ensure all loads for this order are delivered and POD verified
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
                // تعليق: استخدم خدمة الدفع بدلاً من التغيير المباشر
                // EN: Use payment service instead of direct status change
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
        Chat::system($thread, "🧾 حالة الطلب: {$from} → {$next}");
        return $this->ok(new OrderResource($order->fresh()->load(['buyer','seller','listing'])));
    }
}
