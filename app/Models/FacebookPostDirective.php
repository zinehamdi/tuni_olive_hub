<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookPostDirective extends Model
{
    use HasFactory;

    protected $table = 'facebook_post_directives';

    protected $fillable = [
        'post_id',
        'post_url',
        'title',
        'hook_goal',
        'custom_prompt',
        'target_action_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
