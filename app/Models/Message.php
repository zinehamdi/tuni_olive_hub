<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['thread_id','sender_id','body','attachments','is_flagged','is_deleted','is_hidden'];
    protected $casts = [ 'attachments' => 'array', 'is_flagged'=>'boolean','is_deleted'=>'boolean','is_hidden'=>'boolean' ];
    public function thread() { return $this->belongsTo(Thread::class); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
}
