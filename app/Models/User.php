<?php
/**
 * User Model - نموذج المستخدم
 * 
 * Represents a registered user in the system with role-based access
 * يمثل مستخدم مسجل في النظام مع صلاحيات حسب الدور
 * 
 * @property int $id معرّف المستخدم
 * @property string $name الاسم
 * @property string $phone رقم الهاتف
 * @property string $email البريد الإلكتروني
 * @property string $password كلمة المرور (مشفرة)
 * @property string $role الدور (farmer, carrier, mill, packer, normal, admin) - (مزارع، ناقل، معصرة، مُعبّئ، عادي، مدير)
 * @property string $role_subtype نوع الدور الفرعي
 * @property string $locale اللغة (ar, en, fr) - (عربي، إنجليزي، فرنسي)
 * @property string $kyc_status حالة التحقق من الهوية
 * @property bool $is_verified تم التحقق منه
 * @property float $rating_avg متوسط التقييم
 * @property int $rating_count عدد التقييمات
 * @property float $trust_score درجة الثقة
 * @property \Illuminate\Support\Carbon|null $banned_at تاريخ الحظر
 * @property \Illuminate\Support\Carbon|null $onboarding_completed_at تاريخ إتمام التسجيل
 * @property string|null $farm_name اسم المزرعة
 * @property string|null $company_name اسم الشركة
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Address> $addresses العناوين
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Product> $products المنتجات
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Listing> $listings العروض
 * 
 * @package App\Models
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $onboarding_completed_at
 * @property string $password
 * @property string $role
 * @property string|null $role_subtype
 * @property string $locale
 * @property string $kyc_status
 * @property bool $is_verified
 * @property string $rating_avg
 * @property int $rating_count
 * @property int $trust_score
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string|null $remember_token
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $default_mill_addr_id
 * @property string|null $farm_name
 * @property string|null $location
 * @property string|null $company_name
 * @property int|null $fleet_size
 * @property string|null $mill_name
 * @property int|null $capacity
 * @property string|null $packer_name
 * @property string|null $packaging_types
 * @property string|null $full_name
 * @property string|null $farm_name_ar
 * @property string|null $farm_name_lat
 * @property string|null $olive_type
 * @property string|null $farm_location
 * @property int|null $tree_number
 * @property string|null $profile_picture
 * @property array<array-key, mixed>|null $cover_photos
 * @property int|null $camion_capacity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Listing> $listings
 * @property-read int|null $listings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCamionCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCoverPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDefaultMillAddrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFarmLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFarmName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFarmNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFarmNameLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFleetSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKycStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMillName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOliveType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOnboardingCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePackagingTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePackerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRatingAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRatingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleSubtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTreeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTrustScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','phone','email','password','role','role_subtype','locale','kyc_status','is_verified',
        'rating_avg','rating_count','trust_score','banned_at',
        // Onboarding fields
        'onboarding_completed_at',
        'farm_name','farm_name_ar','farm_name_lat','location', // farmer
        'farm_location','tree_number','profile_picture','cover_photos',
        'company_name','fleet_size', // carrier
        'camion_capacity', // carrier
        'mill_name','capacity', // mill
        'packer_name','packaging_types', // packer
        'full_name', // normal
        'olive_type', // olive tree type
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'banned_at' => 'datetime',
        'cover_photos' => 'array',
    ];

    /**
     * Get the user's addresses
     * الحصول على عناوين المستخدم
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the user's products
     * الحصول على منتجات المستخدم
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Get the user's listings
     * الحصول على عروض المستخدم
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
