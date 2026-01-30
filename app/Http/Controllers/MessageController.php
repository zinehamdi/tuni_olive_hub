<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Show inbox with all conversations
     */
    public function inbox()
    {
        $user = auth()->user();
        
        // Get all threads where user is a participant
        $threads = Thread::where('object_type', 'direct_message')
            ->whereJsonContains('participants', $user->id)
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->get()
            ->map(function($thread) use ($user) {
                // Get the other participant
                $otherUserId = collect($thread->participants)->first(fn($id) => $id != $user->id);
                $otherUser = User::find($otherUserId);
                
                $lastMessage = $thread->messages->first();
                $unreadCount = Message::where('thread_id', $thread->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_hidden', false)
                    ->whereNull('read_at')
                    ->count();
                
                return [
                    'thread' => $thread,
                    'other_user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter(fn($item) => $item['other_user'] !== null)
            ->sortByDesc(fn($item) => $item['last_message']?->created_at);
        
        return view('messages.inbox', compact('threads'));
    }

    /**
     * Show conversation with a specific user
     */
    public function show(User $user)
    {
        $authUser = auth()->user();
        
        if ($authUser->id === $user->id) {
            return redirect()->route('messages.inbox')->with('error', __('You cannot message yourself'));
        }
        
        // Find or create thread
        $thread = $this->findOrCreateThread($authUser->id, $user->id);
        
        // Get messages
        $messages = Message::where('thread_id', $thread->id)
            ->where('is_hidden', false)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark messages as read
        Message::where('thread_id', $thread->id)
            ->where('sender_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return view('messages.show', compact('user', 'thread', 'messages'));
    }

    /**
     * Send a message to a user
     */
    public function send(Request $request, User $user): JsonResponse
    {
        $authUser = auth()->user();
        
        if ($authUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('You cannot message yourself'),
            ], 400);
        }
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);
        
        // Find or create thread
        $thread = $this->findOrCreateThread($authUser->id, $user->id);
        
        // Create message
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $authUser->id,
            'body' => $validated['message'],
            'is_flagged' => false,
            'is_deleted' => false,
            'is_hidden' => false,
        ]);
        
        // Update thread timestamp
        $thread->touch();
        
        return response()->json([
            'success' => true,
            'message' => __('Message sent successfully'),
            'data' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at->format('M d, Y H:i'),
            ],
        ]);
    }

    /**
     * Get messages for a thread (for AJAX polling)
     */
    public function getMessages(User $user): JsonResponse
    {
        $authUser = auth()->user();
        
        $thread = $this->findOrCreateThread($authUser->id, $user->id);
        
        $messages = Message::where('thread_id', $thread->id)
            ->where('is_hidden', false)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'is_mine' => $msg->sender_id === $authUser->id,
                'created_at' => $msg->created_at->format('M d, Y H:i'),
            ]);
        
        // Mark as read
        Message::where('thread_id', $thread->id)
            ->where('sender_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        
        $count = Message::whereHas('thread', function($q) use ($user) {
                $q->where('object_type', 'direct_message')
                  ->whereJsonContains('participants', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_hidden', false)
            ->whereNull('read_at')
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Find or create a direct message thread between two users
     */
    private function findOrCreateThread(int $userId1, int $userId2): Thread
    {
        $participants = [$userId1, $userId2];
        sort($participants);
        
        // Find existing thread
        $thread = Thread::where('object_type', 'direct_message')
            ->where(function($q) use ($participants) {
                $q->whereJsonContains('participants', $participants[0])
                  ->whereJsonContains('participants', $participants[1]);
            })
            ->first();
        
        if (!$thread) {
            $thread = Thread::create([
                'object_type' => 'direct_message',
                'object_id' => 0,
                'participants' => $participants,
            ]);
        }
        
        return $thread;
    }
}
