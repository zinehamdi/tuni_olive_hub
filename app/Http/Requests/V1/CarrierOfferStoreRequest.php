<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\Models\Load;
use Illuminate\Foundation\Http\FormRequest;

class CarrierOfferStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'offer_price' => ['required','numeric','min:0'],
            'eta_minutes' => ['required','integer','min:1'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($v){
            /** @var Load $load */
            $load = $this->route('load');
            if ($load) {
                $price = (float) $this->input('offer_price');
                $min = $load->price_floor !== null ? (float) $load->price_floor : null;
                $max = $load->price_ceiling !== null ? (float) $load->price_ceiling : null;
                if (($min !== null && $price < $min) || ($max !== null && $price > $max)) {
                    $v->errors()->add('offer_price', trans('validation.offer_out_of_range'));
                }
            }
        });
    }
}
