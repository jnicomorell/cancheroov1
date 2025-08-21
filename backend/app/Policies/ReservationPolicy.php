<?php

namespace App\Policies;

use App\Models\{Reservation, Role, User};

class ReservationPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, [Role::CLIENTE, Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id ||
            in_array($user->role, [Role::ADMIN, Role::SUPERADMIN], true);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $this->update($user, $reservation);
    }
}

