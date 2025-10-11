<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = ['actor_id','target_id','object_type','object_id','reason','evidence','status'];
    protected $casts = ['evidence' => 'array'];

    public function actor(){ return $this->belongsTo(User::class, 'actor_id'); }
    public function target(){ return $this->belongsTo(User::class, 'target_id'); }
}
