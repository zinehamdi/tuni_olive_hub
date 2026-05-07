<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAnalytic extends Model
{
    protected $fillable = [
        'event_type', 'service_id', 'value', 'currency', 'session_id', 'user_id'
    ];
}
