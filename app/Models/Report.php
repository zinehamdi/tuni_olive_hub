<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['reporter_id','object_type','object_id','reason','evidence','status'];
    protected $casts = ['evidence' => 'array'];

    public function reporter(){ return $this->belongsTo(User::class, 'reporter_id'); }
}
