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
        if (count($participants) >= 2) {
            $sortedParticipants = [(int) $participants[0], (int) $participants[1]];
            sort($sortedParticipants);

            // Find existing direct_message thread between these participants
            $thread = Thread::where('object_type', 'direct_message')
                ->where(function($q) use ($sortedParticipants) {
                    $q->whereJsonContains('participants', $sortedParticipants[0])
                      ->whereJsonContains('participants', $sortedParticipants[1]);
                })
                ->first();

            if ($thread) {
                return $thread;
            }

            // Create unified direct_message thread
            return Thread::create([
                'object_type' => 'direct_message',
                'object_id' => 0,
                'participants' => $sortedParticipants,
            ]);
        }

        return Thread::firstOrCreate([
            'object_type' => $type,
            'object_id' => $id,
        ], [ 'participants' => $participants ]);
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
