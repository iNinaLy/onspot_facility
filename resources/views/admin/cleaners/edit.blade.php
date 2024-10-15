@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 900px; font-family: 'Helvetica Neue', Arial, sans-serif;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Edit Cleaner</h1>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
    <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" class="p-4 rounded" style="background: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 12px;">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required value="{{ old('username', $cleaner->cleaner_username) }}" placeholder="Username" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $cleaner->cleaner_name) }}" placeholder="Name" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <div class="mb-3">
            <label for="phone_no" class="form-label">Phone Number</label>
            <input type="text" name="phone_no" class="form-control" id="phone_no" required value="{{ old('phone_no', $cleaner->cleaner_phoneNo) }}" placeholder="Phone Number" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <div class="mb-3">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" id="status" required style="border-radius: 8px; border: 1px solid #ced4da;">
                <option value="" disabled>Select Status</option>
                <option value="Available" {{ old('status', $cleaner->status) == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Unavailable" {{ old('status', $cleaner->status) == 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="New Password" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm New Password" style="border-radius: 8px; border: 1px solid #ced4da;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 8px; background-color: #2E5675;">Update Cleaner</button>
    </form>
</div>
@endsection
