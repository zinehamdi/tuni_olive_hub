<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use App\Models\Load;
use Illuminate\Foundation\Http\FormRequest;

class TripStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'carrier_id' => ['required','integer','exists:users,id'],
            'load_ids' => ['nullable','array','min:1'],
            'load_ids.*' => ['integer','exists:loads,id'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function($v){
            $loadIds = (array) $this->input('load_ids', []);
            if (!count($loadIds)) return;
            $loads = Load::whereIn('id', $loadIds)->get();
            if ($loads->isEmpty()) return;
            $carrierId = $this->input('carrier_id');
            $allMatched = $loads->every(fn($l) => $l->status === Load::ST_MATCHED && (string)$l->carrier_id === (string)$carrierId);
            if (!$allMatched) {
                $v->errors()->add('load_ids', trans('auth.forbidden_action'));
            }
        });
    }
}
