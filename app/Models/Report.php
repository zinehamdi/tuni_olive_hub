<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $reporter_id
 * @property string $object_type
 * @property int $object_id
 * @property string $reason
 * @property array<array-key, mixed>|null $evidence
 * @property string $status
 * @property int|null $moderator_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $reporter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereEvidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereModeratorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = ['reporter_id','object_type','object_id','reason','evidence','status'];
    protected $casts = ['evidence' => 'array'];

    public function reporter(){ return $this->belongsTo(User::class, 'reporter_id'); }
}
