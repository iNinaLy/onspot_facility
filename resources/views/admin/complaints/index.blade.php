@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 1200px;">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Manage Complaints</h1>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Sort by Status (Enhanced Sort Bar) -->
    <div class="sort-bar mb-4 d-flex justify-content-center align-items-center">
        <form method="GET" action="{{ route('admin.complaints') }}" class="d-flex" style="width: 100%; max-width: 600px; gap: 1rem;">
            <label for="status" class="form-label visually-hidden">Sort by Status</label>
            <select name="status" id="status" class="form-control" style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="on going" {{ request('status') == 'on going' ? 'selected' : '' }}>On Going</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button class="btn btn-primary" type="submit" style="border-radius: 8px; padding: 10px 20px; background-color: #4C7F9D; border: none;">Sort</button>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="table-responsive">
        @if($complaints->isEmpty())
            <div class="alert alert-info text-center">
                No complaints found.
            </div>
        @else
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Officer</th>
                    <th>Supervisor</th>
                    <th>Assigned Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->id }}</td>
                    <td>{{ $complaint->comp_date }} {{ $complaint->comp_time }}</td>
                    <td>{{ $complaint->comp_location }}</td>
                    <td>
                        @if($complaint->comp_status == 'pending')
                            <span style="color: #FFD966;">Pending</span>
                        @elseif($complaint->comp_status == 'on going')
                            <span style="color: #A7D2CB;">On Going</span>
                        @else
                            <span style="color: #B4D3A8;">Completed</span>
                        @endif
                    </td>
                    <td>
                        @if($complaint->officer)
                            <div class="d-flex align-items-center">
                                <!-- Display Profile Picture -->
                                @if($complaint->officer->profile_pic)
                                    <img src="{{ asset('storage/'.$complaint->officer->profile_pic) }}" alt="Profile Picture" class="profile-img me-2" />
                                @else
                                    <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Picture" class="profile-img me-2" />
                                @endif
                                <!-- Display Officer Name -->
                                <span>{{ $complaint->officer->name }}</span>
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $complaint->supervisor->name ?? 'N/A' }}</td>
                    <td>{{ $complaint->assigned_date }}</td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <a href="{{ route('admin.complaints.edit', $complaint->id) }}" class="btn btn-sm" style="background-color: #fff; color: #000; border-radius: 8px; border: 1px solid #000; padding: 0.5rem 1.5rem; font-weight: 400;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm" style="background-color: #000; color: #fff; border-radius: 8px; border: none;" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $complaint->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $complaint->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $complaint->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this complaint?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End of modal -->
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $complaints->appends(request()->query())->links() }}
        </div>
        @endif
    </div> <!-- End of table-responsive -->
</div> <!-- End of container -->

<!-- Styling for Images -->
<style>
    .profile-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }
</style>
@endsection
