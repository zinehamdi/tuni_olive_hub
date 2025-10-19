<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Financing;
use Illuminate\Http\Request;
use App\Services\Chat;

class FinancingController extends ApiController
{
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $data = $request->validate([
            'carrier_id' => ['nullable','integer','exists:users,id'],
            'qty_target' => ['required','numeric','min:0.001'],
            'amount' => ['required','numeric','min:0'],
            'price_per_kg' => ['nullable','numeric','min:0'],
            'terms' => ['nullable','string','max:2000'],
        ]);
        $f = Financing::create([
            'financer_id' => $user->id,
            'carrier_id' => $data['carrier_id'] ?? null,
            'qty_target' => $data['qty_target'],
            'delivered_qty' => 0,
            'amount' => $data['amount'],
            'price_per_kg' => $data['price_per_kg'] ?? null,
            'terms' => $data['terms'] ?? null,
            'status' => 'proposed',
        ]);
        $this->audit('financing.proposed', 'financing', $f->id);
        $thread = Chat::ensureThread('financing', $f->id, [$f->financer_id, $f->carrier_id]);
        Chat::system($thread, '💵 تم اقتراح تمويل.');
        return $this->ok($f, 201);
    }

    public function accept(Request $request, Financing $financing)
    {
        $user = $request->user();
        if ($financing->carrier_id) {
            if ((int)$user->id !== (int)$financing->carrier_id && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        } else {
            if ($user->role !== 'carrier' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
            $financing->carrier_id = $user->id;
        }
        if ($financing->status !== 'proposed') abort(422, 'Invalid state.');
        $financing->status = 'active';
        $financing->save();
        $this->audit('financing.accepted', 'financing', $financing->id);
        $thread = Chat::ensureThread('financing', $financing->id, [$financing->financer_id, $financing->carrier_id]);
        Chat::system($thread, '✅ تم قبول التمويل.');
        return $this->ok($financing);
    }

    public function settle(Request $request, Financing $financing)
    {
        $user = $request->user();
        if ((int)$user->id !== (int)$financing->financer_id && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        if ($financing->status !== 'active') abort(422, 'Invalid state.');
        if ((float)$financing->delivered_qty < (float)$financing->qty_target) abort(422, 'Target not reached');
        $financing->status = 'settled';
        $financing->save();
        $this->audit('financing.settled', 'financing', $financing->id);
        $thread = Chat::ensureThread('financing', $financing->id, [$financing->financer_id, $financing->carrier_id]);
        Chat::system($thread, '🧾 تم تسوية التمويل.');
        return $this->ok($financing);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = Financing::query();
        if ($user->role === 'mill') $q->where('financer_id', $user->id);
        if ($user->role === 'carrier') $q->where(function($qq) use ($user){ $qq->whereNull('carrier_id')->orWhere('carrier_id', $user->id); });
        if ($user->role === 'admin') { /* see all */ }
        $q->latest('id');
        $per = max(1, min(100, (int)$request->input('per_page', 15)));
        $p = $q->paginate($per)->appends($request->query());
        $meta = [
            'current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage(), 'from' => $p->firstItem(), 'to' => $p->lastItem(),
        ];
        $data = collect($p->items())->map(function($f){
            $suggest_settle = $f->status === 'active' && (float)$f->delivered_qty >= (float)$f->qty_target;
            return array_merge($f->toArray(), ['suggest_settle' => $suggest_settle]);
        });
        return response()->json(['success' => true, 'data' => $data, 'meta' => $meta]);
    }
}
