<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;
    protected $fillable = ['object_type','object_id','participants'];
    protected $casts = [ 'participants' => 'array' ];
    public function messages() { return $this->hasMany(Message::class); }
}
