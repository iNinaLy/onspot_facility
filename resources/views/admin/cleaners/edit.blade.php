@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 900px; font-family: 'Helvetica Neue', Arial, sans-serif;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Edit Cleaner</h1>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Cleaner Form -->
    <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" enctype="multipart/form-data" class="p-4 rounded" style="background: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 12px;">
        @csrf
        @method('PUT')

        <!-- Username Field -->
        <div class="mb-4">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required value="{{ old('username', $cleaner->cleaner_username) }}" placeholder="Enter Username" style="border-radius: 8px;">
        </div>

        <!-- Name Field -->
        <div class="mb-4">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $cleaner->cleaner_name) }}" placeholder="Enter Name" style="border-radius: 8px;">
        </div>

        <!-- Phone Number Field -->
        <div class="mb-4">
            <label for="phone_no" class="form-label">Phone Number</label>
            <input type="text" name="phone_no" class="form-control" id="phone_no" required value="{{ old('phone_no', $cleaner->cleaner_phoneNo) }}" placeholder="Enter Phone Number" style="border-radius: 8px;">
        </div>

        <!-- Profile Picture Field -->
        <div class="mb-4">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic" style="border-radius: 8px;">
            @if($cleaner->profile_pic)
                <img src="{{ asset('storage/' . $cleaner->profile_pic) }}" alt="Profile Picture" width="100" class="img-thumbnail mt-2">
                <div class="mt-2">
                    <label class="form-label">Current Profile Picture</label>
                </div>
            @endif
        </div>

        <!-- Status Field -->
        <div class="mb-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" id="status" required style="border-radius: 8px;">
                <option value="Available" {{ old('status', $cleaner->status) == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Unavailable" {{ old('status', $cleaner->status) == 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <!-- New Password Field -->
        <div class="mb-4">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password" style="border-radius: 8px;">
        </div>

        <!-- Confirm New Password Field -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm New Password" style="border-radius: 8px;">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px; background-color: #2E5675;">Update Cleaner</button>
    </form>
</div>
@endsection
