@extends('layouts.admin')

@section('content')
<div class="max-w-lg mx-auto my-10 p-8 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Edit Cleaner Information</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form to edit a cleaner -->
    <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="flex flex-col">
            <label for="cleaner_name" class="font-semibold text-sm text-gray-700 mb-1">Name</label>
            <input type="text" name="cleaner_name" id="cleaner_name" value="{{ old('cleaner_name', $cleaner->cleaner_name) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <div class="flex flex-col">
            <label for="username" class="font-semibold text-sm text-gray-700 mb-1">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', $cleaner->username) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="flex flex-col">
            <label for="cleaner_phoneNo" class="font-semibold text-sm text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="cleaner_phoneNo" id="cleaner_phoneNo" value="{{ old('cleaner_phoneNo', $cleaner->cleaner_phoneNo) }}" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div class="flex flex-col">
            <label for="status" class="font-semibold text-sm text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <option value="available" {{ old('status', $cleaner->status) == 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ old('status', $cleaner->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md shadow-md hover:bg-blue-700 transition-all duration-200 font-semibold">Save</button>
    </form>
</div>
@endsection
