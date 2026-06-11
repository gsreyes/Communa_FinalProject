@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Units</h1>
                <p class="mt-1 text-sm text-gray-600">Manage condominium units before assigning residents.</p>
            </div>
            <a href="{{ route('units.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">New Unit</a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Area</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Active Residents</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($units as $unit)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $unit->unit_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($unit->area_sqm, 2) }} sqm</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $unit->active_residents_count }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('units.show', $unit) }}" class="font-medium text-blue-600 hover:text-blue-800">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No units found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $units->links() }}</div>
    </div>
</div>
@endsection
