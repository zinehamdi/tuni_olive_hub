<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public $timestamps = false;
    protected $fillable = ['blocker_id','blocked_user_id','reason','created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function blocker(){ return $this->belongsTo(User::class, 'blocker_id'); }
    public function blocked(){ return $this->belongsTo(User::class, 'blocked_user_id'); }
}
