@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Payment History</h1>

                <!-- Filter Tabs -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('payments.index') }}" class="py-2 px-1 border-b-2 font-medium text-sm {{ $status ? 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' : 'border-blue-500 text-blue-600' }}">
                            All
                        </a>
                        <a href="{{ route('payments.index', ['status' => 'completed']) }}" class="py-2 px-1 border-b-2 font-medium text-sm {{ $status === 'completed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Completed
                        </a>
                        <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="py-2 px-1 border-b-2 font-medium text-sm {{ $status === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Pending
                        </a>
                        <a href="{{ route('payments.index', ['status' => 'failed']) }}" class="py-2 px-1 border-b-2 font-medium text-sm {{ $status === 'failed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Failed
                        </a>
                    </nav>
                </div>

                <!-- Payments Table -->
                @if($payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Bill #</th>
                                    @if(auth()->user()->isBillingStaff())
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Resident</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $payment->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($payment->bill)
                                                {{ $payment->bill->id }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        @if(auth()->user()->isBillingStaff())
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $payment->user->name }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            PHP {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No payments found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
