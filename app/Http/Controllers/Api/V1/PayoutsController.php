<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayoutsController extends ApiController
{
    public function show(Payout $payout)
    {
        $this->authorize('show', $payout);
        return $this->ok(['payout' => $payout]);
    }

    public function request(Request $request)
    {
        $data = Validator::make($request->all(), [
            'payee_id' => 'required|exists:users,id',
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:TND,USD,EUR',
            'provider' => 'required|in:bank,transfer,manual',
            'meta' => 'nullable|array',
        ])->validate();
        $this->authorize('request', [Payout::class, $data]);
        $data['status'] = 'pending';
        $payout = Payout::create($data);
        $this->audit('payout_request', 'payout', $payout->id);
        return $this->ok(['payout' => $payout], 201);
    }

    // Generic provider webhook: header X-Payout-Signature, secret from config('services.payouts.webhook_secret')
    public function webhook(Request $request)
    {
        $secret = (string) config('services.payouts.webhook_secret');
        $signature = (string) $request->header('X-Payout-Signature', '');
        $payload = $request->getContent();
        $calc = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($calc, $signature)) {
            Log::warning('Invalid payout webhook signature');
            return response()->json(['success' => false], 401);
        }
        $data = json_decode($payload, true) ?: [];
        $payoutId = $data['payout_id'] ?? null;
    $eventId = (string) ($data['event_id'] ?? ($data['id'] ?? ''));
        $status = (string) ($data['status'] ?? '');
    if (!$payoutId || !$eventId) return response()->json(['success' => true]);
        $payout = Payout::find($payoutId);
        if (!$payout) return response()->json(['success' => true]);

        $meta = $payout->meta ?? [];
        $events = $meta['events'] ?? [];
        if ($eventId) {
            $existing = collect($events)->first(fn($e) => ($e['event_id'] ?? '') === $eventId);
            if ($existing) { return response()->json(['success' => true]); }
        }

        $map = [
            'processing' => 'processing',
            'paid' => 'paid',
            'failed' => 'failed',
        ];
        if (($map[$status] ?? null) && $payout->status !== $map[$status]) {
            $payout->status = $map[$status];
        }
        $events[] = [
            'event_id' => $eventId,
            'status' => $status,
            'at' => now()->toISOString(),
        ];
        $meta['events'] = $events;
        $payout->meta = $meta;
        $payout->save();
        $this->audit('payout_webhook_event', 'payout', $payout->id);
        return response()->json(['success' => true]);
    }
}
