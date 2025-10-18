<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * الحصول على قواعد التحقق المطبقة على الطلب
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_picture' => ['nullable', 'image'], // Accept any image format/size
            'cover_photos.*' => ['nullable', 'image'], // Accept any image format/size
            'remove_cover_photos' => ['nullable', 'string'],
            
            // Farmer fields - حقول المزارع
            'farm_name' => ['nullable', 'string', 'max:255'],
            'farm_name_ar' => ['nullable', 'string', 'max:255'],
            'tree_number' => ['nullable', 'integer', 'min:0'],
            'olive_type' => ['nullable', 'string', 'max:255'],
            
            // Carrier fields - حقول الناقل
            'company_name' => ['nullable', 'string', 'max:255'],
            'fleet_size' => ['nullable', 'integer', 'min:0'],
            'camion_capacity' => ['nullable', 'numeric', 'min:0'],
            
            // Mill fields - حقول المعصرة
            'mill_name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            
            // Packer fields - حقول المُعبّئ
            'packer_name' => ['nullable', 'string', 'max:255'],
            'packaging_types' => ['nullable', 'string', 'max:255'],
        ];
    }
}
