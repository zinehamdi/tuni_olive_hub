<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $actor_id
 * @property int $target_id
 * @property string $object_type
 * @property int $object_id
 * @property string $reason
 * @property array<array-key, mixed>|null $evidence
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $actor
 * @property-read \App\Models\User $target
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dispute extends Model
{
    use HasFactory;

    protected $fillable = ['actor_id','target_id','object_type','object_id','reason','evidence','status'];
    protected $casts = ['evidence' => 'array'];

    public function actor(){ return $this->belongsTo(User::class, 'actor_id'); }
    public function target(){ return $this->belongsTo(User::class, 'target_id'); }
}
