<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'seller_id','type','variety','quality','is_organic','volume_liters','weight_kg','price','stock','media','is_premium','export_ready','certs'
    ];
    protected $casts = [
        'media' => 'array',
        'is_organic' => 'boolean',
        'is_premium' => 'boolean',
        'export_ready' => 'boolean',
        'certs' => 'array',
    ];
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function listings() { return $this->hasMany(Listing::class); }
}
