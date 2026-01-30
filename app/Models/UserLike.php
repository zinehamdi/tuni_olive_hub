<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLike extends Model
{
    protected $fillable = ['liker_id', 'liked_id'];

    /**
     * Get the user who liked
     */
    public function liker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liker_id');
    }

    /**
     * Get the user being liked
     */
    public function liked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liked_id');
    }
}
