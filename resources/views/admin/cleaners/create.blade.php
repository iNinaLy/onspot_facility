@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Add New Cleaner</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form to create a cleaner -->
    <form action="{{ route('admin.cleaners.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="cleaner_name" class="form-label">Cleaner Name</label>
            <input type="text" name="cleaner_name" class="form-control" id="cleaner_name" required>
        </div>
        <div class="mb-3">
            <label for="cleaner_phoneNo" class="form-label">Phone Number</label>
            <input type="text" name="cleaner_phoneNo" class="form-control" id="cleaner_phoneNo" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Cleaner</button>
    </form>
</div>
@endsection