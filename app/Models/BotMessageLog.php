<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotMessageLog extends Model
{
    use HasFactory;

    protected $table = 'bot_messages_log';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'sender',
        'message_text',
        'channel',
        'latency_seconds',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(BotConversation::class, 'conversation_id');
    }
}
