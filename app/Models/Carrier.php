<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','vehicle_type','capacity_liters','preferred_govs','docs','rating','verified_at'];
    protected $casts = ['preferred_govs'=>'array','docs'=>'array','verified_at'=>'datetime'];
    public function user(){ return $this->belongsTo(User::class); }
}
