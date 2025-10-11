<?php
/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $role_subtype
 * @property string $locale
 * @property string $kyc_status
 * @property bool $is_verified
 * @property float $rating_avg
 * @property int $rating_count
 * @property float $trust_score
 * @property \Illuminate\Support\Carbon|null $banned_at
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'farm_location','tree_number','profile_picture',
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
    ];

    // Relationships
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
}
