@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 1200px;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Manage Supervisors</h1>
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
        <form method="GET" action="{{ route('admin.supervisors.index') }}"> 
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="{{ request()->query('search') }}" placeholder="Search by name, phone number, or email" aria-label="Search" style="border-radius: 8px; border: 1px solid #ced4da;">
                <button class="btn btn-primary" type="submit" style="border-radius: 8px;">Search</button>
            </div>
        </form>
    </div>

    <!-- Supervisors Table -->
    <div class="table-responsive">
        @if($supervisors->isEmpty())
            <div class="alert alert-info text-center">
                No supervisors found.
            </div>
        @else
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($supervisors as $supervisor)
                <tr>
                    <!-- Profile Picture Column -->
                    <td>
                        @if ($supervisor->profile_pic)
                            <img src="{{ asset('storage/' . $supervisor->profile_pic) }}" alt="Profile Picture" class="profile-img">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>

                    <!-- Supervisor Details -->
                    <td>{{ $supervisor->name ?? 'Not Available' }}</td>
                    <td>{{ $supervisor->email ?? 'Not Available' }}</td>
                    <td>{{ $supervisor->phone_no ?? 'Not Available' }}</td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <a href="{{ route('admin.supervisors.edit', $supervisor->id) }}" class="btn btn-sm" style="background-color: #fff; color: #000; border-radius: 8px; border: 1px solid #000; padding: 0.5rem 1.5rem; font-weight: 400;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm" style="background-color: #000; color: #fff; border-radius: 8px; border: none;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $supervisor->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{ $supervisor->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $supervisor->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $supervisor->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete {{ $supervisor->name }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.supervisors.destroy', $supervisor->id) }}" method="POST">
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
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $supervisors->appends(request()->query())->links() }}
        </div>
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
