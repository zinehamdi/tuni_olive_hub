<?php
/**
 * واجهة خدمة الدفع
 * PaymentService Interface
 */
namespace App\Services\Payments;

interface PaymentService
{
    public function authorize(array $data): array;
    public function capture(array $data): array;
    public function refund(array $data): array;
}
