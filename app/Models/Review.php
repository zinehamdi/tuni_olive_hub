<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $reviewer_id
 * @property int $target_user_id
 * @property string|null $object_type
 * @property int|null $object_id
 * @property int $rating
 * @property string|null $title
 * @property string|null $comment
 * @property array<array-key, mixed>|null $photos
 * @property bool $is_verified_purchase
 * @property bool $is_visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReviewReply> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $reviewer
 * @property-read \App\Models\User $target
 * @method static \Database\Factories\ReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsVerifiedPurchase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereTargetUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
