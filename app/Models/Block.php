<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $blocker_id
 * @property int $blocked_user_id
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $blocked
 * @property-read \App\Models\User $blocker
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block whereBlockedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block whereBlockerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Block whereReason($value)
 * @mixin \Eloquent
 */
class Block extends Model
{
    public $timestamps = false;
    protected $fillable = ['blocker_id','blocked_user_id','reason','created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function blocker(){ return $this->belongsTo(User::class, 'blocker_id'); }
    public function blocked(){ return $this->belongsTo(User::class, 'blocked_user_id'); }
}
