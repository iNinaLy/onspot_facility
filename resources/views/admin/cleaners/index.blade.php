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

<div class="container my-5" style="max-width: 1200px;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Manage Cleaners</h1>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.cleaners') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, phone number, or username" aria-label="Search" style="border-radius: 8px; border: 1px solid #ced4da;">
                <button class="btn btn-primary" type="submit" style="border-radius: 8px;">Search</button>
            </div>
        </form>
    </div>

    <!-- Cleaners Table -->
    <div class="table-responsive">
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cleaners as $cleaner)
                <tr>
                    <td>{{ $cleaner->cleaner_name }}</td>
                    <td>{{ $cleaner->cleaner_phoneNo }}</td>
                    <td>{{ $cleaner->cleaner_username }}</td>
                    <td>
                        @if($cleaner->status == 'Available')
                            <span style="color: #276678;">{{ $cleaner->status }}</span>
                        @elseif($cleaner->status == 'Unavailable')
                            <span style="color: #C0392B;">{{ $cleaner->status }}</span>
                        @else
                            <span style="color: #7D7D7D;">{{ $cleaner->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <a href="{{ route('admin.cleaners.edit', $cleaner->id) }}" class="btn btn-sm" style="background-color: #fff; color: #000; border-radius: 8px; border: 1px solid #000; padding: 0.5rem 1.5rem; font-weight: 400;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm" style="background-color: #000; color: #fff; border-radius: 8px; border: none;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cleaner->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div> <!-- End of btn-group -->

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{ $cleaner->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $cleaner->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $cleaner->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete {{ $cleaner->cleaner_name }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST">
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
    </div> <!-- End of table-responsive -->
</div> <!-- End of container -->
@endsection
