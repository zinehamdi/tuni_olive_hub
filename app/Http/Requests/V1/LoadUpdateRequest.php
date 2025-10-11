<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoadUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'kind' => ['sometimes','in:oil,olive'],
            'qty' => ['sometimes','numeric'],
            'unit' => ['sometimes','in:l,kg,ton'],
            'pickup_addr_id' => ['sometimes','integer','exists:addresses,id'],
            'dropoff_addr_id' => ['sometimes','integer','exists:addresses,id'],
            'deadline_at' => ['sometimes','date'],
            'price_floor' => ['sometimes','numeric'],
            'price_ceiling' => ['sometimes','numeric'],
            'status' => ['sometimes','in:new,matched,in_transit,delivered,settled'],
        ];
    }
}
