<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListingStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'product_id' => ['required','integer','exists:products,id'],
            'seller_id' => ['required','integer','exists:users,id'],
            'status' => ['nullable','in:draft,active,paused,sold,out'],
            'min_order' => ['nullable','numeric'],
            'payment_methods' => ['nullable','array'],
            'delivery_options' => ['nullable','array'],
        ];
    }
}
