<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;
    protected $fillable = ['payee_id','invoice_id','amount','currency','status','provider','provider_ref','meta'];
    protected $casts = [ 'meta'=>'array' ];
    public function payee(){ return $this->belongsTo(User::class, 'payee_id'); }
    public function invoice(){ return $this->belongsTo(Invoice::class); }
}
