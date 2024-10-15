@extends('layouts.admin')

@section('content')
<style>
    /* General Styles */
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    /* Button Hover Effect */
    .btn:hover {
        background-color: #276678; /* Change to a different color on hover */
        color: #fff; /* Change text color to white */
        border-color: #276678; /* Match border color with the background */
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Edit Button Styles */
    .btn-edit {
        background-color: white;
        color: #276678; /* Match the text color with the theme */
        border: 1px solid #276678; /* Border to match the text */
    }

    /* Delete Button Styles */
    .btn-delete {
        background-color: black;
        color: white; /* Text color for delete button */
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 1rem;
        font-weight: 600;
        color: #333;
        background-color: #F7F7F7;
        border-bottom: 2px solid #EAEAEA;
    }

    td {
        padding: 1rem;
        font-weight: 400;
        border-bottom: 1px solid #EAEAEA;
        transition: background-color 0.3s;
    }

    tr:hover {
        background-color: #f1f1f1; /* Light gray on hover */
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        border-bottom: none;
    }
</style>

<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Complaints</h1>

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" class="form-control" placeholder="Search by name, phone number, or username">
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $complaint->comp_id }}</td>
                        <td>{{ $complaint->comp_date }}</td>
                        <td>{{ $complaint->comp_time }}</td>
                        <td>{{ $complaint->comp_desc }}</td>
                        <td>{{ $complaint->comp_location }}</td>
                        <td>{{ $complaint->comp_status }}</td>
                        <td>
                            <!-- Edit button -->
                            <a href="{{ route('admin.complaints.edit', $complaint->comp_id) }}" class="btn btn-edit btn-sm">Edit</a>
                            
                            <!-- Delete button trigger -->
                            <button type="button" class="btn btn-delete btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $complaint->comp_id }}">
                                Delete
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal{{ $complaint->comp_id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $complaint->comp_id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $complaint->comp_id }}">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this complaint?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.complaints.destroy', $complaint->comp_id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $complaints->links() }} <!-- Add pagination links -->
    </div>
</div>
@endsection
