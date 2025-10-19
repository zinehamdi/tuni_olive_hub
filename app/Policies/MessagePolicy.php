<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use App\Policies\Concerns\HandlesAdmin;

class MessagePolicy
{
    use HandlesAdmin;

    public function view(User $user, Message $message): bool
    {
        $thread = $message->thread;
        $participants = (array) ($thread->participants ?? []);
        return $this->isAdmin($user) || in_array($user->id, $participants, true);
    }

    public function create(User $user): bool { return !empty($user->id); }

    public function update(User $user, Message $message): bool
    {
        return $this->isAdmin($user) || $message->sender_id === $user->id;
    }

    public function delete(User $user, Message $message): bool
    {
        return $this->isAdmin($user) || $message->sender_id === $user->id;
    }
}
