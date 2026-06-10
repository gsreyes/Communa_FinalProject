@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Billing Statements</h1>
                    @if(auth()->user()->isBillingStaff())
                        <a href="{{ route('bills.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Create New Bill
                        </a>
                    @endif
                </div>

                <!-- Summary Cards -->
                @php
                    $stats = app('App\Http\Controllers\BillController')->getStats();
                @endphp
                
                @if($stats)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Bills</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_bills'] }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Paid Amount</p>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($stats['paid'], 2) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Unpaid Amount</p>
                            <p class="text-2xl font-bold text-red-600">₱{{ number_format($stats['unpaid'], 2) }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Overdue Amount</p>
                            <p class="text-2xl font-bold text-orange-600">₱{{ number_format($stats['overdue'], 2) }}</p>
                        </div>
                    </div>
                @endif

                <!-- Bills Table -->
                @if($bills->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Unit</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Due Date</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($bills as $bill)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $bill->unit->unit_number ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $bill->billType->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            ₱{{ number_format($bill->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $bill->due_date->format('M d, Y') }}
                                            @if($bill->isOverdue())
                                                <p class="text-red-600 text-xs">Overdue</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $bill->status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                            ">
                                                {{ $bill->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                View
                                            </a>
                                            @if($bill->status === 'Unpaid' && auth()->user()->isResident())
                                                <a href="{{ route('payments.create', $bill) }}" class="text-green-600 hover:text-green-900">
                                                    Pay
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $bills->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No bills found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection