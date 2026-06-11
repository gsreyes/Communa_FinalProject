@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">Create Unit</h1>
            <form action="{{ route('units.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @include('units.partials.form', ['unit' => $unit])
                <div class="flex justify-end gap-3">
                    <a href="{{ route('units.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
