<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BotConversation extends Model
{
    use HasFactory;

    protected $table = 'bot_conversations';

    protected $fillable = [
        'channel',
        'external_id',
        'user_name',
        'phone_number',
        'intent',
        'status',
        'last_user_message',
        'last_bot_reply',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(BotMessageLog::class, 'conversation_id')->orderBy('created_at', 'asc');
    }
}
