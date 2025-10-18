<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $review_id
 * @property int $replier_id
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $replier
 * @property-read \App\Models\Review $review
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereReplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereReviewId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReviewReply whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = ['review_id','replier_id','body'];

    public function review(){ return $this->belongsTo(Review::class); }
    public function replier(){ return $this->belongsTo(User::class, 'replier_id'); }
}
