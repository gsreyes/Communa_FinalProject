@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Bill #{{ $bill->id }}</h1>
                        <p class="text-gray-500">Created {{ $bill->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('bills.index') }}" class="text-blue-600 hover:text-blue-900">Back to Bills</a>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $bill->status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                    ">
                        {{ $bill->status }}
                    </span>
                </div>

                <!-- Bill Details Grid -->
                <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b">
                    <div>
                        <p class="text-sm text-gray-600">Unit</p>
                        <p class="text-lg font-semibold">{{ $bill->unit->unit_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Resident</p>
                        <p class="text-lg font-semibold">{{ $bill->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Bill Type</p>
                        <p class="text-lg font-semibold">{{ $bill->billType->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Due Date</p>
                        <p class="text-lg font-semibold">
                            {{ $bill->due_date->format('M d, Y') }}
                            @if($bill->isOverdue())
                                <span class="text-red-600 text-xs">(Overdue)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Amount Section -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-lg font-semibold mb-4">Amount Details</h2>
                    <div class="grid grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Amount Due</p>
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($bill->amount, 2) }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Amount Paid</p>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($bill->paid_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Balance</p>
                            <p class="text-2xl font-bold text-red-600">₱{{ number_format($bill->amount - ($bill->paid_amount ?? 0), 2) }}</p>
                        </div>
                    </div>

                    @if($bill->description)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="text-gray-700">{{ $bill->description }}</p>
                        </div>
                    @endif

                    @if($bill->billing_period_start && $bill->billing_period_end)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Billing Period</p>
                            <p class="text-gray-700">{{ $bill->billing_period_start->format('M d, Y') }} - {{ $bill->billing_period_end->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Payments Section -->
                @if($bill->payments->count() > 0)
                    <div class="mb-8 pb-8 border-b">
                        <h2 class="text-lg font-semibold mb-4">Payment History</h2>
                        <div class="space-y-3">
                            @foreach($bill->payments as $payment)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-semibold">₱{{ number_format($payment->amount, 2) }}</p>
                                        <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($payment->status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <div>
                        @if(auth()->user()->isBillingStaff())
                            <a href="{{ route('bills.edit', $bill) }}" class="text-blue-600 hover:text-blue-900 mr-4">
                                Edit
                            </a>
                            @if($bill->status === 'Unpaid')
                                <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                    @if($bill->status === 'Unpaid' && auth()->user()->isResident())
                        <a href="{{ route('payments.create', $bill) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                            Pay Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection