<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExportOffer;
use App\Models\User;
use App\Policies\Concerns\HandlesAdmin;

class ExportOfferPolicy
{
    use HandlesAdmin;

    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, ExportOffer $offer): bool { return true; }
    public function create(User $user): bool { return in_array($user->role, ['exporter','packer','mill','admin'], true); }
    public function update(User $user, ExportOffer $offer): bool { return $this->isAdmin($user) || $offer->seller_id === $user->id; }
    public function delete(User $user, ExportOffer $offer): bool { return $this->isAdmin($user) || $offer->seller_id === $user->id; }
}
