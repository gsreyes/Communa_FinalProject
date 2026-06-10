@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Edit Ticket #{{ $ticket->id }}</h1>

                <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required>
                            <option value="Pending" {{ $ticket->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="Rejected" {{ $ticket->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assign To -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign To Admin
                        </label>
                        <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Unassigned --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admin Response -->
                    <div>
                        <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                            Response
                        </label>
                        <textarea name="admin_response" id="admin_response" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required>{{ $ticket->admin_response }}</textarea>
                        @error('admin_response')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('tickets.show', $ticket) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                            Update Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection