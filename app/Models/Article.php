<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'content',
        'image',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'category' => 'array',
        'content' => 'array',
        'is_active' => 'boolean',
    ];
}
