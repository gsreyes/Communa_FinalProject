@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Unit {{ $unit->unit_number }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ number_format($unit->area_sqm, 2) }} sqm</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('units.edit', $unit) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Edit</a>
                    <form action="{{ route('units.destroy', $unit) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete this unit?')" class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900">Residents</h2>
                <div class="mt-3 overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="divide-y divide-gray-200">
                            @forelse($unit->users as $resident)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $resident->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->pivot->occupant_type }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->pivot->is_active ? 'Active' : 'Inactive' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-8 text-center text-sm text-gray-500">No residents assigned.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
