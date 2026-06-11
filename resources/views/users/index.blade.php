@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Users</h1>
                <p class="mt-1 text-sm text-gray-600">Manage residents, admins, and billing staff.</p>
            </div>
            <a href="{{ route('users.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">New User</a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->units->pluck('unit_number')->join(', ') ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('users.show', $user) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</div>
@endsection
