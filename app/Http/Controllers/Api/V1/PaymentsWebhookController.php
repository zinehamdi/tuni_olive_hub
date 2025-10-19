<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentsWebhookController extends ApiController
{
    // Simple HMAC verification stubs. Expect headers: X-Signature and body containing order_id and status
    public function flouci(Request $request)
    {
        $secret = (string) config('services.flouci.webhook_secret');
        return $this->handleWebhook($request, $secret, 'flouci');
    }

    public function d17(Request $request)
    {
        $secret = (string) config('services.d17.webhook_secret');
        return $this->handleWebhook($request, $secret, 'd17');
    }

    protected function handleWebhook(Request $request, string $secret, string $provider)
    {
        $signature = (string) $request->header('X-Signature', '');
        $payload = $request->getContent();
        $calc = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($calc, $signature)) {
            Log::warning('Invalid webhook signature', ['provider' => $provider]);
            return response()->json(['success' => false], 401);
        }

        $data = json_decode($payload, true) ?: [];
        $orderId = $data['order_id'] ?? null;
        $eventId = (string) ($data['event_id'] ?? ($data['id'] ?? ($data['txid'] ?? null)));
        $status = (string) ($data['status'] ?? '');
        if (!$orderId) return response()->json(['success' => true]);
        $order = Order::find($orderId);
        if (!$order) return response()->json(['success' => true]);

        // Idempotency: skip if we processed this event
        $meta = $order->meta ?? [];
        $payments = $meta['payments'] ?? [];
        if ($eventId) {
            $existing = collect($payments)->first(fn($p) => ($p['event_id'] ?? '') === $eventId);
            if ($existing && ($existing['status'] ?? '') === $status) {
                // exact duplicate
                return response()->json(['success' => true]);
            }
        }

        // Map provider status to internal payment state
        $map = [
            'authorized' => Order::PAY_AUTH,
            'captured' => Order::PAY_CAPTURED,
            'paid' => Order::PAY_CAPTURED,
            'failed' => Order::PAY_FAILED,
        ];
        $new = $map[$status] ?? null;
        if ($new && $order->payment_status !== $new) {
            $order->payment_status = $new;
        }
        // Persist payload summary
        $payments[] = [
            'provider' => $provider,
            'event_id' => $eventId,
            'status' => $status,
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? 'TND',
            'received_at' => now()->toISOString(),
        ];
        $meta['payments'] = $payments;
        $order->meta = $meta;
        $order->save();
        $this->audit('payment_webhook', 'order', $order->id);
        if (($map[$status] ?? null) === Order::PAY_CAPTURED) {
            event(new OrderPaid($order->id, 'webhook:'.$provider));
        }
        return response()->json(['success' => true]);
    }
}
