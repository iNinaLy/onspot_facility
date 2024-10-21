@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 900px;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Edit Complaint Status</h1>
    </div>

    <!-- Display Validation Errors -->
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

    <!-- Complaint Information Card -->
    <div class="card shadow-sm mb-4" style="border-radius: 10px;">
        <div class="card-header" style="background-color: #f8f9fa; font-weight: 500; border-bottom: 1px solid #eaeaea;">
            Complaint Information
        </div>
        <div class="card-body">
            <p><strong>Complaint ID:</strong> {{ $complaint->id }}</p>
            <p><strong>Date:</strong> {{ $complaint->comp_date }}</p>
            <p><strong>Time:</strong> {{ $complaint->comp_time }}</p>
            <p><strong>Description:</strong> {{ $complaint->comp_desc }}</p>
            <p><strong>Location:</strong> {{ $complaint->comp_location }}</p>
        </div>
    </div>

    <!-- Edit Complaint Form -->
    <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Complaint Status -->
        <div class="mb-4">
            <label for="comp_status" class="form-label">Complaint Status</label>
            <select name="comp_status" id="comp_status" class="form-control" style="border-radius: 8px;" required>
                <option value="pending" {{ $complaint->comp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="on going" {{ $complaint->comp_status == 'on going' ? 'selected' : '' }}>On Going</option>
                <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <!-- Back and Save Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.complaints') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 0.5rem 1.5rem;">Back to Complaints</a>
            <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 0.5rem 1.5rem;">Update Status</button>
        </div>
    </form>
</div>
@endsection
