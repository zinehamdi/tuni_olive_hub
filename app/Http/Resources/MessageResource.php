<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'sender' => $this->whenLoaded('sender', function () { return [ 'id' => $this->sender->id, 'name' => $this->sender->name, 'role' => $this->sender->role ]; }),
            'body' => $this->body,
            'attachments' => $this->attachments,
            'is_flagged' => (bool) $this->is_flagged,
            'is_deleted' => (bool) $this->is_deleted,
            'created_at' => $this->created_at,
        ];
    }
}
