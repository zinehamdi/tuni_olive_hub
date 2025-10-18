<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shipment_id
 * @property string $type
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExportShipment $shipment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportDocument whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class ExportDocument extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','type','url','verified_at','meta'];
    protected $casts = [ 'verified_at'=>'datetime','meta'=>'array' ];
    public function shipment(){ return $this->belongsTo(ExportShipment::class, 'shipment_id'); }
}
