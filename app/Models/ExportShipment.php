<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExportDocument;

/**
 * @property int $id
 * @property int $contract_id
 * @property string $incoterm
 * @property string $port_from
 * @property string $port_to
 * @property string|null $vessel
 * @property \Illuminate\Support\Carbon|null $etd_at
 * @property \Illuminate\Support\Carbon|null $eta_at
 * @property string $status
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contract $contract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ExportDocument> $documents
 * @property-read int|null $documents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereEtaAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereEtdAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereIncoterm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment wherePortFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment wherePortTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportShipment whereVessel($value)
 * @mixin \Eloquent
 */
class ExportShipment extends Model
{
    use HasFactory;
    protected $fillable = ['contract_id','incoterm','port_from','port_to','vessel','etd_at','eta_at','status','meta'];
    protected $casts = [ 'etd_at'=>'datetime','eta_at'=>'datetime','meta'=>'array' ];
    public function contract(){ return $this->belongsTo(Contract::class); }
    public function documents(){ return $this->hasMany(ExportDocument::class, 'shipment_id'); }
}
