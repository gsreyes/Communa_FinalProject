@php
    $selectedRole = old('role', $user->role ?? 'resident');
    $selectedUnit = old('unit_id', $user?->units->first()?->id);
@endphp

<div>
    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
    <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input id="password" name="password" type="password" {{ $user ? '' : 'required' }} class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ $user ? '' : 'required' }} class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
        <select id="role" name="role" required class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm" onchange="toggleResidentFields()">
            <option value="resident" {{ $selectedRole === 'resident' ? 'selected' : '' }}>Resident</option>
            <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="billing_staff" {{ $selectedRole === 'billing_staff' ? 'selected' : '' }}>Billing Staff</option>
        </select>
        @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div data-resident-field>
        <label for="resident_type" class="block text-sm font-medium text-gray-700">Resident Type</label>
        <select id="resident_type" name="resident_type" class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
            <option value="owner" {{ old('resident_type', $user->resident_type ?? 'owner') === 'owner' ? 'selected' : '' }}>Owner</option>
            <option value="tenant" {{ old('resident_type', $user->resident_type ?? '') === 'tenant' ? 'selected' : '' }}>Tenant</option>
        </select>
        @error('resident_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div data-resident-field>
        <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit</label>
        <select id="unit_id" name="unit_id" class="mt-1 w-full rounded-md border-gray-300 px-3 py-2 shadow-sm">
            <option value="">Select a unit</option>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ (int) $selectedUnit === $unit->id ? 'selected' : '' }}>{{ $unit->unit_number }}</option>
            @endforeach
        </select>
        @error('unit_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<script>
function toggleResidentFields() {
    const isResident = document.getElementById('role').value === 'resident';
    document.querySelectorAll('[data-resident-field]').forEach((field) => {
        field.style.display = isResident ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', toggleResidentFields);
</script>
