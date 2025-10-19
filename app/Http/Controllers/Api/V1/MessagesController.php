<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;

class MessagesController extends ApiController
{
    public function index(Request $request, Thread $thread)
    {
        $user = $request->user();
        // Authorization: participant or admin
        $participants = (array) ($thread->participants ?? []);
        if ($user->role !== 'admin' && !in_array((int)$user->id, array_map('intval', $participants), true)) {
            abort(403, trans('auth.forbidden_action'));
        }
        $q = Message::query()->where('thread_id', $thread->id)->orderBy('id');
        if ($user->role !== 'admin') { $q->where('is_hidden', false); }
        $items = $q->get();
        return $this->ok($items);
    }
}
