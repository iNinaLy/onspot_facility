@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 900px;">
    <div class="text-center mb-4">
        <h1>Edit Officer Information</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.officers.update', $officer->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Officer Name -->
        <div class="mb-4">
            <label for="name" class="form-label">Officer Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $officer->name) }}" required>
        </div>

        <!-- Officer Email -->
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $officer->email) }}" required>
        </div>

        <!-- Officer Phone Number -->
        <div class="mb-4">
            <label for="phone_no" class="form-label">Phone Number</label>
            <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no', $officer->phone_no) }}" required>
        </div>

        <!-- Profile Picture -->
        <div class="mb-4">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" name="profile_pic">
            @if ($officer->profile_pic)
                <img src="{{ asset('storage/' . $officer->profile_pic) }}" alt="Profile Picture" width="100" class="img-thumbnail mt-2">
                <div class="mt-2">
                    <label class="form-label">Current Profile Picture</label>
                </div>
            @endif
        </div>

        <!-- New Password -->
        <div class="mb-4">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" placeholder="Enter New Password">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
