<title>{{ config('app.name','OnSpot Facility') }}</title>
<link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">

@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Add New Supervisor</h1>

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

    <!-- Form to create a supervisor -->
    <form action="{{ route('admin.supervisors.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="s_name" class="form-label">Supervisor Name</label>
            <input type="text" name="s_name" class="form-control" id="s_name" required>
        </div>
        <div class="mb-3">
            <label for="s_email" class="form-label">Email</label>
            <input type="email" name="s_email" class="form-control" id="s_email" required>
        </div>
        <div class="mb-3">
            <label for="s_phoneNo" class="form-label">Phone Number</label>
            <input type="text" name="s_phoneNo" class="form-control" id="s_phoneNo" required>
        </div>
        <div class="mb-3">
            <label for="s_pass" class="form-label">Password</label>
            <input type="password" name="s_pass" class="form-control" id="s_pass" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Supervisor</button>
    </form>
</div>
@endsection