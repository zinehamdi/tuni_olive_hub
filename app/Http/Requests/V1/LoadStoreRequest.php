<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoadStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'owner_id' => ['required','integer','exists:users,id'],
            'kind' => ['required','in:oil,olive'],
            'qty' => ['required','numeric','min:0.001'],
            'unit' => ['required','in:l,kg,ton'],
            'pickup_addr_id' => ['required','integer','exists:addresses,id'],
            'dropoff_addr_id' => ['required','integer','exists:addresses,id'],
            'deadline_at' => ['nullable','date'],
            'price_floor' => ['nullable','numeric'],
            'price_ceiling' => ['nullable','numeric'],
            'status' => ['nullable','in:new,matched,in_transit,delivered,settled'],
        ];
    }
}
