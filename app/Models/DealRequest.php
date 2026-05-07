<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'name',
        'email',
        'phone',
        'requirements',
        'message',
        'status',
    ];

    protected $casts = [
        'requirements' => 'array',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
