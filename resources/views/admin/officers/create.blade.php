@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Add New Officer</h1>

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

    <!-- Form to create an officer -->
    <form action="{{ route('admin.officers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="officer_name" class="form-label">Officer Name</label>
            <input type="text" name="officer_name" class="form-control" id="officer_name" required value="{{ old('officer_name') }}">
        </div>
        
        <div class="mb-3">
            <label for="officer_email" class="form-label">Email</label>
            <input type="email" name="officer_email" class="form-control" id="officer_email" required value="{{ old('officer_email') }}">
        </div>
        
        <div class="mb-3">
            <label for="officer_phoneNo" class="form-label">Phone Number</label>
            <input type="text" name="officer_phoneNo" class="form-control" id="officer_phoneNo" required value="{{ old('officer_phoneNo') }}">
        </div>
        
        <div class="mb-3">
            <label for="officer_pass" class="form-label">Password</label>
            <input type="password" name="officer_pass" class="form-control" id="officer_pass" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Officer</button>
    </form>
</div>
@endsection
