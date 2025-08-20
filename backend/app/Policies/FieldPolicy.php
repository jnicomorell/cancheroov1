<?php

namespace App\Policies;

use App\Models\{Field, Role, User};

class FieldPolicy
{
    /**
     * Determine whether the user can create fields.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function update(User $user, Field $field): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function delete(User $user, Field $field): bool
    {
        return in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }
    public function update(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }

    public function delete(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }

    public function update(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }

    public function delete(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }

    public function update(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }

    public function delete(User $user, Field $field): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true);
    }
}
