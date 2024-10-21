@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Edit Officer Information</h1>

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

    <!-- Form to edit officer details -->
    <form action="{{ route('admin.officers.update', $officer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="officer_name" class="form-label">Officer Name</label>
            <input type="text" name="officer_name" class="form-control" id="officer_name" value="{{ old('officer_name', $officer->officer_name) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="officer_email" class="form-label">Email</label>
            <input type="email" name="officer_email" class="form-control" id="officer_email" value="{{ old('officer_email', $officer->officer_email) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="officer_phoneNo" class="form-label">Phone Number</label>
            <input type="text" name="officer_phoneNo" class="form-control" id="officer_phoneNo" value="{{ old('officer_phoneNo', $officer->officer_phoneNo) }}" required>
        </div>

        <!-- Optional password update -->
        <div class="mb-3">
            <label for="officer_pass" class="form-label">Password (leave blank to keep current password)</label>
            <input type="password" name="officer_pass" class="form-control" id="officer_pass">
        </div>
        
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
