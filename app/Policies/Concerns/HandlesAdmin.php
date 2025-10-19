<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;

trait HandlesAdmin
{
    protected function isAdmin(?User $user): bool
    {
        return $user?->role === 'admin';
    }
}
