@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold mb-2">Payment for Bill #{{ $bill->id }}</h1>
                    <p class="text-gray-500">{{ $bill->billType->name ?? 'N/A' }} - {{ $bill->unit->unit_number ?? 'N/A' }}</p>
                </div>

                <!-- Amount Section -->
                <div class="mb-8 pb-8 border-b">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Total Due</p>
                            <p class="text-3xl font-bold text-blue-600">PHP {{ number_format($bill->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Amount Paid</p>
                            <p class="text-3xl font-bold text-green-600">PHP {{ number_format($bill->paid_amount ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Amount to Pay Now</p>
                        <p class="text-2xl font-semibold text-gray-900">PHP {{ number_format($bill->amount - ($bill->paid_amount ?? 0), 2) }}</p>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('payments.store', $bill) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Amount Input -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Amount (PHP) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" max="{{ $bill->amount - ($bill->paid_amount ?? 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" placeholder="0.00" required value="{{ $bill->amount - ($bill->paid_amount ?? 0) }}">
                        <p class="text-xs text-gray-500 mt-1">
                            Maximum: PHP {{ number_format($bill->amount - ($bill->paid_amount ?? 0), 2) }}
                        </p>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-900">
                            <strong>Payment Information:</strong><br>
                            You will be redirected to HitPay secure payment gateway.
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('bills.show', $bill) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
