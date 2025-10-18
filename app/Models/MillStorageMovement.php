<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $mill_id
 * @property string $product
 * @property string $type
 * @property string $qty
 * @property string $unit
 * @property string|null $ref_object_type
 * @property int|null $ref_object_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereMillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereRefObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereRefObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MillStorageMovement whereUnit($value)
 * @mixin \Eloquent
 */
class MillStorageMovement extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'mill_id','product','type','qty','unit','ref_object_type','ref_object_id','note','created_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
