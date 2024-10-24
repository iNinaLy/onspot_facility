@extends('layouts.admin')

@section('content')
<div class="container mx-auto my-10 px-6 max-w-screen-md">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Edit Complaint Status</h1>
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

    <!-- Complaint Information Card -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Complaint Information</h2>
        <div class="grid grid-cols-1 gap-4">
            <div><strong>Complaint ID:</strong> {{ $complaint->id }}</div>
            <div><strong>Date:</strong> {{ $complaint->comp_date }}</div>
            <div><strong>Time:</strong> {{ $complaint->comp_time }}</div>
            <div><strong>Description:</strong> {{ $complaint->comp_desc }}</div>
            <div><strong>Location:</strong> {{ $complaint->comp_location }}</div>
        </div>
    </div>

    <!-- Edit Complaint Form -->
    <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Complaint Status -->
        <div class="mb-6">
            <label for="comp_status" class="block text-gray-700 font-medium mb-2">Complaint Status</label>
            <select name="comp_status" id="comp_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="pending" {{ $complaint->comp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="on going" {{ $complaint->comp_status == 'on going' ? 'selected' : '' }}>On Going</option>
                <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <a href="{{ route('admin.complaints') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Back to Complaints</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update Status</button>
        </div>
    </form>
</div>

<!-- Styling -->
<style>
    .form-control {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
        width: 100%;
        margin-top: 0.5rem;
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

    .btn-close {
        cursor: pointer;
    }
</style>
@endsection
