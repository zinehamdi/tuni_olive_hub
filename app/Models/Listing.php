<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','seller_id','status','min_order','payment_methods','delivery_options'];
    protected $casts = [
        'payment_methods' => 'array',
        'delivery_options' => 'array',
    ];
    public function product() { return $this->belongsTo(Product::class); }
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
}
