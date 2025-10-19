<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use App\Policies\Concerns\HandlesAdmin;

class ListingPolicy
{
    use HandlesAdmin;

    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Listing $listing): bool { return true; }
    public function create(User $user): bool { return in_array($user->role, ['farmer','mill','packer','trader_carrier','exporter','admin'], true); }
    public function update(User $user, Listing $listing): bool { return $this->isAdmin($user) || $listing->seller_id === $user->id; }
    public function delete(User $user, Listing $listing): bool { return $this->isAdmin($user) || $listing->seller_id === $user->id; }
}
