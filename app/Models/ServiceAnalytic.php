<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAnalytic extends Model
{
    protected $fillable = [
        'event_type', 'service_id', 'value', 'currency', 'session_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(MarketingService::class, 'service_id');
    }

    /**
     * Resolves the user identity by stitching browser sessions
     */
    public function getLikelyUserAttribute()
    {
        if ($this->user_id) {
            return $this->user;
        }

        // Search for any other log with the same persistent device cookie UUID that has a user_id
        $stitchedLog = self::where('session_id', $this->session_id)
            ->whereNotNull('user_id')
            ->latest()
            ->first();

        return $stitchedLog ? $stitchedLog->user : null;
    }
}
