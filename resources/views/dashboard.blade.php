@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-medium uppercase text-blue-700">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
            <h1 class="mt-1 text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome back, {{ auth()->user()->name }}.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            @forelse($stats as $label => $value)
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium capitalize text-gray-500">{{ str_replace('_', ' ', $label) }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        @if(str_contains($label, 'paid') || str_contains($label, 'unpaid') || str_contains($label, 'payments_total'))
                            PHP {{ number_format($value, 2) }}
                        @else
                            {{ number_format($value) }}
                        @endif
                    </p>
                </div>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-gray-600">No dashboard stats are available for this account yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            @if(auth()->user()->isResident() || auth()->user()->isAdmin())
                <a href="{{ route('tickets.index') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm hover:border-blue-300 hover:shadow">
                    <p class="font-semibold text-gray-900">Tickets</p>
                    <p class="mt-1 text-sm text-gray-600">Review requests and concerns.</p>
                </a>
            @endif
            @if(auth()->user()->isResident() || auth()->user()->isBillingStaff())
                <a href="{{ route('bills.index') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm hover:border-blue-300 hover:shadow">
                    <p class="font-semibold text-gray-900">Bills</p>
                    <p class="mt-1 text-sm text-gray-600">View statements and balances.</p>
                </a>
                <a href="{{ route('payments.index') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm hover:border-blue-300 hover:shadow">
                    <p class="font-semibold text-gray-900">Payments</p>
                    <p class="mt-1 text-sm text-gray-600">Check payment history.</p>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
