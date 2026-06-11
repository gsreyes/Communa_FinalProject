<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    // /**
    //  * Create a new policy instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }
    /**
     * Determine if the user can view the ticket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin can view all tickets
        if ($user->isAdmin()) {
            return true;
        }

        // Resident can only view their own tickets
        return $user->id === $ticket->user_id;
    }

    /**
     * Determine if the user can create tickets
     */
    public function create(User $user): bool
    {
        return $user->isResident();
    }

    /**
     * Determine if the user can update the ticket
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Only admins can update tickets
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Admin can delete any ticket
        if ($user->isAdmin()) {
            return true;
        }

        // Resident can only delete their own pending tickets
        return $user->id === $ticket->user_id && $ticket->status === 'Pending';
    }
}
