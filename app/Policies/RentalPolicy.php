<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rental;
use Illuminate\Auth\Access\Response;

class RentalPolicy
{
    public function modify(User $user, Rental $rental)
    {
        return $user->id === $rental->user_id ? Response::allow() : Response::deny("You don't have permission");
    }
}
