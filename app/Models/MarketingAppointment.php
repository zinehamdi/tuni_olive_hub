<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingAppointment extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'business_info',
        'appointment_date',
        'cart_data',
        'total_budget',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'cart_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
