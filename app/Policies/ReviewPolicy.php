<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\Concerns\HandlesAdmin;

class ReviewPolicy
{
    use HandlesAdmin;

    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Review $review): bool { return $review->is_visible || $this->isAdmin($user) || $review->target_user_id === $user?->id; }
    // Allow any authenticated user to create a review; downstream logic enforces verified purchase
    public function create(User $user): bool { return (bool) $user; }
    public function update(User $user, Review $review): bool { return $this->isAdmin($user) || $review->reviewer_id === $user->id; }
    public function delete(User $user, Review $review): bool { return $this->isAdmin($user) || $review->reviewer_id === $user->id || $review->target_user_id === $user->id; }
}
