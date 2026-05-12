<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'capacity',
        'current_volume',
        'type',
        'variety',
        'acidity',
        'quality'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
