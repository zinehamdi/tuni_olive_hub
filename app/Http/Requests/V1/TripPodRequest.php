<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class TripPodRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'pickup_photos' => ['nullable','array'],
            'dropoff_photos' => ['required','array','min:1'],
            'dropoff_photos.*' => ['string'],
            'signed_name' => ['required','string'],
            'signed_pin' => ['nullable','string'],
            'qr_token' => ['nullable','string'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function($v){
            $photos = (array) $this->input('dropoff_photos', []);
            if (empty($photos)) {
                $v->errors()->add('dropoff_photos', trans('validation.pod_missing_photo'));
            }
            $pin = $this->input('signed_pin');
            $qr = $this->input('qr_token');
            if (!$pin && !$qr) {
                $v->errors()->add('signed_pin', trans('validation.pod_missing_token'));
            }
        });
    }
}
