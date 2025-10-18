<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $actor_id
 * @property string $action
 * @property string|null $object_type
 * @property int|null $object_id
 * @property string|null $ip
 * @property string|null $ua
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $actor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUa($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['actor_id','action','object_type','object_id','ip','ua','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function actor(){ return $this->belongsTo(User::class, 'actor_id'); }
}
