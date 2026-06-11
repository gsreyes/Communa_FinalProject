<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
     public function index()
    {
        $user = Auth::user();

        $stats = [];

        if ($user->isAdmin()) {
            $stats = [
                'tickets_total' => Ticket::count(),
                'tickets_pending' => Ticket::where('status', 'Pending')->count(),
                'tickets_resolved' => Ticket::where('status', 'Resolved')->count(),
            ];
        } elseif ($user->isBillingStaff()) {
            $stats = [
                'bills_total' => Bill::count(),
                'bills_unpaid' => Bill::where('status', 'Unpaid')->sum('amount'),
                'bills_paid' => Bill::where('status', 'Paid')->sum('amount'),
                'payments_pending' => Payment::pending()->count(),
            ];
        } else {
            $stats = [
                'tickets_total' => $user->tickets()->count(),
                'bills_unpaid' => $user->bills()->where('status', 'Unpaid')->sum('amount'),
                'bills_paid' => $user->bills()->where('status', 'Paid')->sum('amount'),
                'payments_total' => $user->payments()->completed()->sum('amount'),
            ];
        }

        return view('dashboard', compact('stats'));
    }
}
