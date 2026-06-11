@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Ticket #{{ $ticket->id }}</h1>
                        <p class="text-gray-500">Submitted {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-900">Back to Tickets</a>
                </div>

                <!-- Status Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($ticket->status === 'Pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($ticket->status === 'Resolved')
                            bg-green-100 text-green-800
                        @else
                            bg-red-100 text-red-800
                        @endif
                    ">
                        {{ $ticket->status }}
                    </span>
                </div>

                <!-- Ticket Details Grid -->
                <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b">
                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="text-lg font-semibold">{{ ucfirst($ticket->type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-semibold">{{ $ticket->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Unit</p>
                        <p class="text-lg font-semibold">{{ $ticket->unit->unit_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Submitted By</p>
                        <p class="text-lg font-semibold">{{ $ticket->user->name }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-lg font-semibold mb-3">Description</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>

                    @if($ticket->attachment)
                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $ticket->attachment) }}" class="text-blue-600 hover:text-blue-900">
                                Download Attachment
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Admin Response -->
                @if($ticket->status !== 'Pending')
                    <div class="mb-8 pb-8 border-b">
                        <h2 class="text-lg font-semibold mb-3">Response</h2>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->admin_response }}</p>
                        @if($ticket->resolved_at)
                            <p class="text-sm text-gray-500 mt-4">Resolved {{ $ticket->resolved_at->diffForHumans() }}</p>
                        @endif
                    </div>
                @endif

                <!-- Admin Edit Section -->
                @if(auth()->user()->isAdmin())
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold mb-4">Admin Actions</h2>
                        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assign To
                                </label>
                                <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ (int) $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                                    <option value="Pending" {{ $ticket->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Rejected" {{ $ticket->status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                                    Response
                                </label>
                                <textarea name="admin_response" id="admin_response" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $ticket->admin_response }}</textarea>
                                @error('admin_response')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                Update Ticket
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
