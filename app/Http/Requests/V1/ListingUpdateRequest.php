<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListingUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'status' => ['sometimes','in:draft,active,paused,sold,out'],
            'min_order' => ['sometimes','numeric'],
            'payment_methods' => ['sometimes','array'],
            'delivery_options' => ['sometimes','array'],
        ];
    }
}
