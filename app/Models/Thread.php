<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property array<array-key, mixed>|null $participants
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Thread extends Model
{
    use HasFactory;
    protected $fillable = ['object_type','object_id','participants'];
    protected $casts = [ 'participants' => 'array' ];
    public function messages() { return $this->hasMany(Message::class); }
}
