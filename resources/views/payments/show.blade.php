@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Payment #{{ $payment->id }}</h1>
                        <p class="text-gray-500">{{ $payment->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-900">Back to Payments</a>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($payment->status === 'completed')
                            bg-green-100 text-green-800
                        @elseif($payment->status === 'pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($payment->status === 'failed')
                            bg-red-100 text-red-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b">
                    <div>
                        <p class="text-sm text-gray-600">Transaction ID</p>
                        <p class="text-lg font-semibold">{{ $payment->hitpay_transaction_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Amount Paid</p>
                        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="text-lg font-semibold">{{ ucfirst($payment->payment_method ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payer</p>
                        <p class="text-lg font-semibold">{{ $payment->user->name }}</p>
                    </div>
                </div>

                <!-- Bill Information -->
                @if($payment->bill)
                    <div class="mb-8 pb-8 border-b">
                        <h2 class="text-lg font-semibold mb-4">Associated Bill</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Bill #</p>
                                <p class="text-lg font-semibold">{{ $payment->bill->id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Bill Type</p>
                                <p class="text-lg font-semibold">{{ $payment->bill->billType->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Original Amount</p>
                                <p class="text-lg font-semibold">₱{{ number_format($payment->bill->amount, 2) }}</p>
                            </div>
                            <div>
                                <a href="{{ route('bills.show', $payment->bill) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                                    View Bill →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Error Information (if failed) -->
                @if($payment->status === 'failed' && $payment->failure_reason)
                    <div class="mb-8 pb-8 border-b">
                        <h2 class="text-lg font-semibold mb-2 text-red-600">Failure Reason</h2>
                        <p class="text-gray-700">{{ $payment->failure_reason }}</p>
                    </div>
                @endif

                <!-- Payment Confirmation -->
                @if($payment->status === 'completed' && $payment->paid_at)
                    <div class="mb-8 pb-8 border-b bg-green-50 p-4 rounded-lg">
                        <p class="text-green-800">
                            <strong>✓ Payment Confirmed</strong><br>
                            Completed on {{ $payment->paid_at->format('M d, Y H:i A') }}
                        </p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end">
                    <button onclick="window.print()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection