<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportDocument extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id','type','url','verified_at','meta'];
    protected $casts = [ 'verified_at'=>'datetime','meta'=>'array' ];
    public function shipment(){ return $this->belongsTo(ExportShipment::class, 'shipment_id'); }
}
