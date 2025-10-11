<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['actor_id','action','object_type','object_id','ip','ua','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function actor(){ return $this->belongsTo(User::class, 'actor_id'); }
}
