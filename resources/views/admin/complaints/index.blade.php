@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Complaints</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5">Complaint List</h2>
    </div>

    <!-- Table to list complaints -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Complaint ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->id }}</td>
                        <td>{{ $complaint->comp_id }}</td>
                        <td>{{ $complaint->comp_date }}</td>
                        <td>{{ $complaint->comp_time }}</td>
                        <td>{{ $complaint->comp_desc }}</td>
                        <td>{{ $complaint->comp_location }}</td>
                        <td>{{ $complaint->comp_status }}</td>
                        <td>
                            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this complaint?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection