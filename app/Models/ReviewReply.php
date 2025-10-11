<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = ['review_id','replier_id','body'];

    public function review(){ return $this->belongsTo(Review::class); }
    public function replier(){ return $this->belongsTo(User::class, 'replier_id'); }
}
