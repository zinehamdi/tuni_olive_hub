<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string|null $vehicle_type
 * @property int|null $capacity_liters
 * @property array<array-key, mixed>|null $preferred_govs
 * @property array<array-key, mixed>|null $docs
 * @property string $rating
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereCapacityLiters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereDocs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier wherePreferredGovs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereVehicleType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrier whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class Carrier extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','vehicle_type','capacity_liters','preferred_govs','docs','rating','verified_at'];
    protected $casts = ['preferred_govs'=>'array','docs'=>'array','verified_at'=>'datetime'];
    public function user(){ return $this->belongsTo(User::class); }
}
