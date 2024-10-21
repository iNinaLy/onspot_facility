@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 900px;">
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Edit Supervisor Information</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #f8d7da; border-color: #f5c6cb;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.supervisors.update', $supervisor->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Supervisor Name -->
        <div class="mb-4">
            <label for="name" class="form-label">Supervisor Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $supervisor->name) }}" style="border-radius: 8px;" required>
        </div>

        <!-- Supervisor Email -->
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $supervisor->email) }}" style="border-radius: 8px;" required>
        </div>

        <!-- Supervisor Phone Number -->
        <div class="mb-4">
            <label for="phone_no" class="form-label">Phone Number</label>
            <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no', $supervisor->phone_no) }}" style="border-radius: 8px;" required>
        </div>

        <!-- Profile Picture -->
        <div class="mb-4">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" name="profile_pic" style="border-radius: 8px;">
            @if ($supervisor->profile_pic)
                <img src="{{ asset('storage/' . $supervisor->profile_pic) }}" alt="Profile Picture" width="100" class="img-thumbnail mt-2">
                <div class="mt-2">
                    <label class="form-label">Current Profile Picture</label>
                </div>
            @endif
        </div>

        <!-- New Password -->
        <div class="mb-4">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" placeholder="Enter New Password" style="border-radius: 8px;">
        </div>

        <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 0.75rem 1.5rem;">Save</button>
    </form>
</div>
@endsection
