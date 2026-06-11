<div>
    <label for="unit_number" class="block text-sm font-medium text-gray-700">Unit Number</label>
    <input id="unit_number" name="unit_number" value="{{ old('unit_number', $unit->unit_number ?? '') }}" required class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
    @error('unit_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="area_sqm" class="block text-sm font-medium text-gray-700">Area (sqm)</label>
    <input id="area_sqm" name="area_sqm" type="number" min="0.01" step="0.01" value="{{ old('area_sqm', $unit->area_sqm ?? '') }}" required class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
    @error('area_sqm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
