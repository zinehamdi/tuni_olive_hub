<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotCustomRule extends Model
{
    use HasFactory;

    protected $table = 'bot_custom_rules';

    protected $fillable = [
        'keyword',
        'match_type',
        'action_type',
        'action_payload',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
