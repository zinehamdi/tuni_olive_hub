<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $thread_id
 * @property int $sender_id
 * @property string|null $body
 * @property array<array-key, mixed>|null $attachments
 * @property bool $is_flagged
 * @property bool $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_hidden
 * @property-read \App\Models\User $sender
 * @property-read \App\Models\Thread $thread
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory;
    protected $fillable = ['thread_id','sender_id','body','attachments','is_flagged','is_deleted','is_hidden'];
    protected $casts = [ 'attachments' => 'array', 'is_flagged'=>'boolean','is_deleted'=>'boolean','is_hidden'=>'boolean' ];
    public function thread() { return $this->belongsTo(Thread::class); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
}
