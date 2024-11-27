<title>{{ config('app.name','OnSpot Facility') }}</title>
<link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">

@extends('layouts.admin')

@section('content')

<style>
    /* Custom Select Dropdown */
    .custom-select {
        border-radius: 12px;
        border: 1px solid #ced4da;
        padding: 0.5rem 1rem;
        background-color: #f9f9f9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .custom-select:focus {
        outline: none;
        border-color: #4C7F9D;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Custom Update Button */
    .custom-update-button {
        display: flex;
        align-items: center;
        padding: 0.3rem 1.2rem;
        background-color: #4C7F9D;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .custom-update-button:hover {
        background-color: #3a6781;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .custom-update-button i {
        margin-right: 4px;
    }

    .profile-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    .no-underline {
        text-decoration: none;
    }
</style>

<div class="container my-5" style="max-width: 1200px;">
    <div class="text-center mb-4">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #333;">Manage Complaints</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" 
             style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        @if($complaints->isEmpty())
            <div class="alert alert-info text-center">No complaints found.</div>
        @else
        <table class="table align-middle text-center">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Complaint by</th>
                    <th>Assigned by</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->id }}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center">
                            <form method="POST" action="{{ route('admin.complaints.updateStatus', $complaint->id) }}" class="d-flex align-items-center">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select custom-select me-2">
                                    <option value="pending" {{ $complaint->comp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ongoing" {{ $complaint->comp_status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="btn custom-update-button">
                                    <i class="bi bi-check-circle"></i> Update
                                </button>
                            </form>
                        </div>
                    </td>

                    <td>{{ $complaint->comp_location }}</td>

                    <td>
                        @if($complaint->officer)
                            <div class="d-flex align-items-center">
                                @if($complaint->officer->profile_pic)
                                    <img src="{{ asset('storage/' . $complaint->officer->profile_pic) }}" 
                                         alt="Profile Picture" class="profile-img me-2">
                                @else
                                    <span>No Image</span>
                                @endif
                                <span>{{ $complaint->officer->name }}</span>
                            </div>
                        @else
                            N/A
                        @endif
                    </td>

                    <td>{{ $complaint->supervisor->name ?? 'N/A' }}</td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <button type="button" class="btn btn-link text-black p-0 no-underline" data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal{{ $complaint->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>

                        <div class="modal fade" id="deleteModal{{ $complaint->id }}" tabindex="-1" 
                            aria-labelledby="deleteModalLabel{{ $complaint->id }}" aria-hidden="true">
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

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $complaints->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@endsection