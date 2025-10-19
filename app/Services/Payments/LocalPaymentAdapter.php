<?php
/**
 * محول دفع محلي للاختبار
 * Local Payment Adapter for testing
 */
namespace App\Services\Payments;

class LocalPaymentAdapter implements PaymentService
{
    public function authorize(array $data): array
    {
        // تعليق: الموافقة دائماً في الوضع المحلي
        // EN: Always authorize in local mode
        return ['status' => 'authorized', 'transaction_id' => uniqid('local_auth_')];
    }
    public function capture(array $data): array
    {
        return ['status' => 'captured', 'transaction_id' => uniqid('local_cap_')];
    }
    public function refund(array $data): array
    {
        return ['status' => 'refunded', 'transaction_id' => uniqid('local_ref_')];
    }
}
