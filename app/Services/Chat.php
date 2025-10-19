<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;

class Chat
{
    public static function ensureThread(string $type, int $id, array $participants = []): Thread
    {
        $thread = Thread::firstOrCreate([
            'object_type' => $type,
            'object_id' => $id,
        ], [ 'participants' => $participants ]);
        return $thread;
    }

    public static function system(Thread $thread, string $body): Message
    {
        return Message::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::id() ?? 1,
            'body' => $body,
            'attachments' => [],
            'is_flagged' => false,
            'is_deleted' => false,
        ]);
    }
}
