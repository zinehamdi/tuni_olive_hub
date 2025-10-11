<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExportDocument;

class ExportShipment extends Model
{
    use HasFactory;
    protected $fillable = ['contract_id','incoterm','port_from','port_to','vessel','etd_at','eta_at','status','meta'];
    protected $casts = [ 'etd_at'=>'datetime','eta_at'=>'datetime','meta'=>'array' ];
    public function contract(){ return $this->belongsTo(Contract::class); }
    public function documents(){ return $this->hasMany(ExportDocument::class, 'shipment_id'); }
}
