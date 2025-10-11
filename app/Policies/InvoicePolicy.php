<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function show(User $user, Invoice $invoice): bool
    {
        return $user->role === 'admin' || $user->id === $invoice->buyer_id || $user->id === $invoice->seller_id;
    }

    // Issue using class-level ability with attributes provided
    public function issue(User $user, $classOrAttributes = null, $maybeAttributes = null): bool
    {
        // Support different call patterns: authorize('issue', [Invoice::class, $data])
        // or authorize('issue', $data)
        $attributes = is_array($classOrAttributes) ? $classOrAttributes : (is_array($maybeAttributes) ? $maybeAttributes : []);
        return $user->role === 'admin' || (isset($attributes['seller_id']) && (int)$attributes['seller_id'] === $user->id);
    }
}
