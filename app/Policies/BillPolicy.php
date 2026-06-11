<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    // /**
    //  * Create a new policy instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }
    /**
     * Determine if the user can view the bill
     */
    public function view(User $user, Bill $bill): bool
    {
        // Admins and billing staff can view all bills.
        if ($user->isAdmin() || $user->isBillingStaff()) {
            return true;
        }

        // Resident can view their own bills
        if ($user->isResident() && $user->id === $bill->user_id) {
            return true;
        }

        // Resident who occupies the unit can view the bill
        if ($user->isResident()) {
            return $user->units()->where('units.id', $bill->unit_id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create bills
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBillingStaff();
    }

    /**
     * Determine if the user can update the bill
     */
    public function update(User $user, Bill $bill): bool
    {
        return $user->isAdmin() || $user->isBillingStaff();
    }

    /**
     * Determine if the user can delete the bill
     */
    public function delete(User $user, Bill $bill): bool
    {
        return ($user->isAdmin() || $user->isBillingStaff()) && $bill->status === 'Unpaid';
    }
}
