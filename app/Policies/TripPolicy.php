<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;
use App\Policies\Concerns\HandlesAdmin;

class TripPolicy
{
    use HandlesAdmin;

    public function view(User $user, Trip $trip): bool { return $this->isAdmin($user) || $trip->carrier_id === $user->id; }
    public function create(User $user): bool { return in_array($user->role, ['carrier','trader_carrier','admin'], true); }
    public function update(User $user, Trip $trip): bool { return $this->isAdmin($user) || $trip->carrier_id === $user->id; }
    public function delete(User $user, Trip $trip): bool { return $this->isAdmin($user); }
}
