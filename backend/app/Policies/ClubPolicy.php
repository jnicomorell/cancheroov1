<?php

namespace App\Policies;

use App\Models\{Club, Role, User};

class ClubPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function update(User $user, Club $club): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function delete(User $user, Club $club): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }
}
