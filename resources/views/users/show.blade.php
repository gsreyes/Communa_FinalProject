@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $user->email }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Edit</a>
                    @if(! $user->is(auth()->user()))
                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this user?')" class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                        </form>
                    @endif
                </div>
            </div>

            <dl class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm text-gray-600">Role</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ str_replace('_', ' ', $user->role) }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm text-gray-600">Resident Type</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $user->resident_type ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm text-gray-600">Unit</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $user->units->pluck('unit_number')->join(', ') ?: 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
