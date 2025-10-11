<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function show(User $user, Payout $payout): bool
    {
        return $user->role === 'admin' || $user->id === $payout->payee_id;
    }

    public function request(User $user, $classOrAttributes = null, $maybeAttributes = null): bool
    {
        $attributes = is_array($classOrAttributes) ? $classOrAttributes : (is_array($maybeAttributes) ? $maybeAttributes : []);
        return $user->role === 'admin' || (isset($attributes['payee_id']) && (int)$attributes['payee_id'] === $user->id);
    }
}
