<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Auth\Access\Response;


class PaymentPolicy
{
    public function modify(Rental $rental, Payment $payment)
    {
        return $rental->id === $payment->rental_id ? Response::allow() : Response::deny("You don't have permission");
    }
}
