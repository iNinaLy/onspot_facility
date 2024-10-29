@extends('layouts.admin')

@section('content')
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

    <div class="sort-bar mb-4 d-flex justify-content-center align-items-center">
        <form method="GET" action="{{ route('admin.complaints.index') }}" class="d-flex" 
              style="width: 100%; max-width: 600px; gap: 1rem;">
            <select name="status" id="status" class="form-control" 
                    style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="on going" {{ request('status') == 'on going' ? 'selected' : '' }}>On Going</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button class="btn btn-primary" type="submit" 
                    style="border-radius: 8px; padding: 10px 20px; background-color: #4C7F9D; border: none;">
                Sort
            </button>
        </form>
    </div>

    <div class="table-responsive">
        @if($complaints->isEmpty())
            <div class="alert alert-info text-center">No complaints found.</div>
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
                    <td>
                        {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }} 
                        {{ \Carbon\Carbon::parse($complaint->comp_time)->format('h:i A') }}
                    </td>
                    <td>{{ $complaint->comp_location }}</td>
                    <td>
                        @php $status = strtolower(trim($complaint->comp_status)); @endphp
                        @if($status === 'pending')
                            <span style="color: #FFD966;">Pending</span>
                        @elseif($status === 'ongoing')
                            <span style="color: #A7D2CB;">Ongoing</span>
                        @elseif($status === 'completed')
                            <span style="color: #B4D3A8;">Completed</span>
                        @else
                            <span style="color: #FF6F61;">Unknown</span>
                        @endif
                    </td>
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
                    <td>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y h:i A') }}</td>
                    <td>
                        <div class="btn-group" role="group" style="gap: 0.75rem;">
                            <a href="{{ route('admin.complaints.edit', $complaint->id) }}" class="btn btn-sm btn-light">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" 
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
