@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 1200px;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Manage Officers</h1>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.officers') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, phone number, or email" aria-label="Search" style="border-radius: 8px; border: 1px solid #ced4da;">
                <button class="btn btn-primary" type="submit" style="border-radius: 8px;">Search</button>
            </div>
        </form>
    </div>

    <!-- Officers Table -->
    <div class="table-responsive">
        @if($officers->isEmpty())
            <div class="no-results">
                No officers found.
            </div>
        @else
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Building</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($officers as $officer)
                <tr>
                    <!-- Profile Picture Column -->
                    <td>
                        @if ($officer->profile_pic)
                            <img src="{{ asset('storage/' . $officer->profile_pic) }}" alt="Profile Picture" class="profile-img">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>

                    <!-- Officer Details -->
                    <td>{{ $officer->name ?? 'Not Available' }}</td>
                    <td>{{ $officer->email ?? 'Not Available' }}</td>
                    <td>{{ $officer->phone_no ?? 'Not Available' }}</td>
                    <td>{{ $officer->building ?? 'Not Set' }}</td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn btn-sm" style="background-color: #fff; color: #000; border-radius: 8px; border: 1px solid #000; padding: 0.5rem 1.5rem; font-weight: 400;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm" style="background-color: #000; color: #fff; border-radius: 8px; border: none;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $officer->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{ $officer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $officer->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $officer->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete {{ $officer->name }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.officers.destroy', $officer->id) }}" method="POST">
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
        @endif
    </div>
</div>

<!-- CSS for Image Styling -->
<style>
    /* Profile Image Styling */
    .profile-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }
</style>
@endsection
