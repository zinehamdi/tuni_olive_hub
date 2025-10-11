<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','contract_id','seller_id','buyer_id','currency','subtotal','tax','total','status','pdf_url','meta'];
    protected $casts = [ 'meta'=>'array' ];
    public function order(){ return $this->belongsTo(Order::class); }
    public function contract(){ return $this->belongsTo(Contract::class); }
    public function seller(){ return $this->belongsTo(User::class, 'seller_id'); }
    public function buyer(){ return $this->belongsTo(User::class, 'buyer_id'); }
}
