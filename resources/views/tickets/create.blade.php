@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Submit a New Ticket</h1>

                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Ticket Type Selection -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Ticket Type
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center">
                                <input type="radio" name="ticket_type" value="concern" class="mr-2" onchange="filterCategories('concern')">
                                <span class="text-sm">Concern/Complaint</span>
                            </label>
                            <label class="relative flex items-center">
                                <input type="radio" name="ticket_type" value="request" class="mr-2" onchange="filterCategories('request')">
                                <span class="text-sm">Request</span>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="ticket_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <select name="ticket_category_id" id="ticket_category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">-- Select a category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}" class="category-option-{{ $category->type }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit
                        </label>
                        <select name="unit_id" id="unit_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">-- Select your unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_number }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" placeholder="Please provide detailed information..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachment (Optional)
                        </label>
                        <input type="file" name="attachment" id="attachment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC, DOCX (Max 5MB)</p>
                        @error('attachment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('tickets.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function filterCategories(type) {
    const select = document.getElementById('ticket_category_id');
    const options = select.querySelectorAll('option[data-type]');
    
    options.forEach(option => {
        if (option.dataset.type === type) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}
</script>
@endsection