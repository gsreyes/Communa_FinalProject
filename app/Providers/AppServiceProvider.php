<?php

namespace App\Providers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Ticket;
use App\Policies\BillPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
    /* 
    Register authorization policies
    */
     protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(Ticket::class, TicketPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Bill::class, BillPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
