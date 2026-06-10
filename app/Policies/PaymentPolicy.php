<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
    // /**
    //  * Create a new policy instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Determine if the user can view the payment
     */
    public function view(User $user, Payment $payment): bool
    {
        // Billing staff can view all payments
        if ($user->isBillingStaff()) {
            return true;
        }

        // User can only view their own payments
        return $user->id === $payment->user_id;
    }

    /**
     * Determine if the user can create payments
     */
    public function create(User $user): bool
    {
        return $user->isResident();
    }
}
