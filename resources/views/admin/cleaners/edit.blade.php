@extends('layouts.admin')

@section('content')
<div class="container mx-auto my-10 px-6 max-w-screen-md">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Edit Cleaner</h1>
        <p class="text-gray-600">Make changes to the cleaner's details below.</p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="text-red-500">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Cleaner Form -->
    <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" enctype="multipart/form-data" class="p-6 bg-white rounded-lg shadow">
        @csrf
        @method('PUT')

        <!-- Username Field -->
        <div class="mb-6">
            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
            <input type="text" name="username" id="username" class="form-control border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('username', $cleaner->cleaner_username) }}" placeholder="Enter Username" required>
        </div>

        <!-- Name Field -->
        <div class="mb-6">
            <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" name="name" id="name" class="form-control border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('name', $cleaner->cleaner_name) }}" placeholder="Enter Name" required>
        </div>

        <!-- Phone Number Field -->
        <div class="mb-6">
            <label for="phone_no" class="block text-gray-700 font-medium mb-2">Phone Number</label>
            <input type="text" name="phone_no" id="phone_no" class="form-control border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('phone_no', $cleaner->cleaner_phoneNo) }}" placeholder="Enter Phone Number" required>
        </div>

        <!-- Profile Picture Field -->
        <div class="mb-6">
            <label for="profile_pic" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" class="form-control border-gray-300 rounded-lg w-full px-4 py-2">

            <!-- Display Current Profile Picture if available -->
            @if($cleaner->profile_pic)
                <div class="mt-4">
                    <label class="block text-gray-700 font-medium mb-2">Current Profile Picture</label>
                    <img src="{{ asset('storage/' . $cleaner->profile_pic) }}" alt="Profile Picture" class="w-24 h-24 rounded-full border-2 border-gray-300 object-cover">
                </div>
            @else
                <p class="text-gray-500 text-sm mt-2">No profile picture uploaded yet.</p>
            @endif
        </div>

        <!-- Status Field -->
        <div class="mb-6">
            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
            <select name="status" id="status" class="form-select border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="available" {{ old('status', $cleaner->status) == 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ old('status', $cleaner->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <!-- New Password Field -->
        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-medium mb-2">New Password (optional)</label>
            <input type="password" name="password" id="password" class="form-control border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter New Password">
        </div>

        <!-- Confirm New Password Field -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm New Password">
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="flex justify-between">
            <a href="{{ route('admin.cleaners') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-all">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all">Update Cleaner</button>
        </div>
    </form>
</div>

<!-- Styling -->
<style>
    .form-control {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.75rem;
        width: 100%;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>
@endsection
