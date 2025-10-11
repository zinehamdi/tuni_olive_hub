<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id','target_user_id','object_type','object_id','rating','title','comment','photos','is_verified_purchase','is_visible'
    ];

    protected $casts = [
        'photos' => 'array',
        'is_verified_purchase' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function reviewer(){ return $this->belongsTo(User::class, 'reviewer_id'); }
    public function target(){ return $this->belongsTo(User::class, 'target_user_id'); }
    public function replies(){ return $this->hasMany(ReviewReply::class); }
}
