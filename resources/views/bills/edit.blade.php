@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Edit Bill #{{ $bill->id }}</h1>

                <form action="{{ route('bills.update', $bill) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Bill Type -->
                    <div>
                        <label for="bill_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bill Type
                        </label>
                        <select name="bill_type_id" id="bill_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required>
                            @foreach($billTypes as $type)
                                <option value="{{ $type->id }}" {{ $bill->bill_type_id === $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bill_type_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount (₱)
                        </label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required value="{{ $bill->amount }}">
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">{{ $bill->description }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="billing_period_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period Start
                            </label>
                            <input type="date" name="billing_period_start" id="billing_period_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" value="{{ $bill->billing_period_start?->format('Y-m-d') }}">
                            @error('billing_period_start')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="billing_period_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period End
                            </label>
                            <input type="date" name="billing_period_end" id="billing_period_end" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" value="{{ $bill->billing_period_end?->format('Y-m-d') }}">
                            @error('billing_period_end')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                        </label>
                        <input type="date" name="due_date" id="due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required value="{{ $bill->due_date->format('Y-m-d') }}">
                        @error('due_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('bills.show', $bill) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                            Update Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection