<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
     public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('dashboards.admin');
        }

        if ($user->isBillingStaff()) {
            return view('dashboards.billing');
        }

        return view('dashboards.resident');
    }
}
