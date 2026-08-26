<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
    return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('unit')) {
            $rawUnit = strtolower(trim((string) $this->input('unit')));
            $unit = match($rawUnit) {
                'liter', 'litre', 'l', 'لتر' => 'l',
                'kilogram', 'kg', 'كلغ' => 'kg',
                'tonne', 'ton', 'طن' => 'ton',
                default => $rawUnit
            };
            $this->merge(['unit' => $unit]);
        }

        if (!$this->has('payment_method') || empty($this->input('payment_method'))) {
            $this->merge(['payment_method' => 'cod']);
        }

        if (!$this->has('buyer_id') || empty($this->input('buyer_id'))) {
            $this->merge(['buyer_id' => Auth::id()]);
        }
    }

    public function rules(): array
    {
        return [
            'buyer_id' => ['required','integer','exists:users,id'],
            'seller_id' => ['required','integer','exists:users,id'],
            'listing_id' => [
                'required',
                Rule::exists('listings', 'id')->where(fn($q) => $q->where('status', 'active')),
            ],
            'qty' => ['required','numeric','min:0.001'],
            'unit' => ['required','in:l,kg,ton,liter,litre,tonne'],
            'price_unit' => ['required','numeric','min:0'],
            'payment_method' => ['required','in:cod,flouci,d17,stripe,bank_lc'],
            'meta' => ['nullable','array'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($v){
            $listing = Listing::with('product')->find($this->input('listing_id'));
            $qty = (float) $this->input('qty');
            if ($listing) {
                if ($listing->min_order !== null && $qty < (float) $listing->min_order) {
                    $v->errors()->add('qty', trans('validation.qty_min_order'));
                }
                $stock = $listing->product?->stock ?? null;
                if ($stock !== null && $qty > (float) $stock) {
                    $v->errors()->add('qty', trans('validation.insufficient_stock'));
                }
            }
        });
    }
}
