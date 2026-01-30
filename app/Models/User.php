<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cover_photos',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'cover_photos' => 'array',
    ];

    /**
     * Get the user's addresses.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the user's products (as seller).
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Get the user's listings (as seller).
     */
    public function listings()
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    /**
     * Get users that this user follows
     * الحصول على المستخدمين الذين يتابعهم هذا المستخدم
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    /**
     * Get users that follow this user
     * الحصول على المتابعين لهذا المستخدم
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    /**
     * Get users that this user likes
     * الحصول على المستخدمين الذين أعجب بهم هذا المستخدم
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liker_id', 'liked_id')
                    ->withTimestamps();
    }

    /**
     * Get users that like this user
     * الحصول على المستخدمين الذين أعجبوا بهذا المستخدم
     */
    public function likers()
    {
        return $this->belongsToMany(User::class, 'user_likes', 'liked_id', 'liker_id')
                    ->withTimestamps();
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * Check if this user likes another user
     */
    public function hasLiked(User $user): bool
    {
        return $this->likedUsers()->where('liked_id', $user->id)->exists();
    }

    /**
     * Get followers count
     */
    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    /**
     * Get likes count
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likers()->count();
    }
}
