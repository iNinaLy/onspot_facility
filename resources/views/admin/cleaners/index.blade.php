@extends('layouts.admin')

@section('title', 'Manage Cleaners')

@push('styles')
<link href="resources/admin/app.css" rel="stylesheet" />

@endpush

@push('scripts')
@vite([
        'resources/admin/app.js',
        'resources/admin/dashboard.js',
        'resources/admin/complaint.js',
    ])
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // Toastr Notifications
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endpush

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="heading">
        <h1 class="header-title">Manage Cleaners</h1>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-5">
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Total Cleaners</h5>
                <p class="card-text">{{ $totalCleaners }}</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Available</h5>
                <p class="card-text">{{ $availableCleaners }}</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Unavailable</h5>
                <p class="card-text">{{ $unavailableCleaners }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('admin.cleaners') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search cleaners..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <!-- Cleaners Table -->
    <div class="table-responsive">
        @if($cleaners->isEmpty())
            <div class="alert alert-info text-center">No cleaners found.</div>
        @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Building</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cleaners as $cleaner)
                <tr>
                    <td>{{ $cleaner->id }}</td>
                    <td>
                        @if($cleaner->profile_pic)
                            <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" 
                                 alt="Profile Picture" class="img-thumbnail rounded-circle" width="50" height="50">
                        @else
                            <span class="text-gray-500">No Image</span>
                        @endif
                    </td>
                    <td>{{ $cleaner->cleaner_name }}</td>
                    <td>{{ $cleaner->cleaner_phoneNo }}</td>
                    <td>{{ $cleaner->cleaner_username }}</td>
                    <td>
                        @if($cleaner->status == 'available')
                            <span class="badge bg-success">{{ ucfirst($cleaner->status) }}</span>
                        @elseif($cleaner->status == 'unavailable')
                            <span class="badge bg-danger">{{ ucfirst($cleaner->status) }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($cleaner->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $cleaner->building ?? 'N/A' }}</td>
                    <td>
                        <!-- Action Buttons with Pastel Colors -->
                        <button type="button" class="btn btn-pastel-view btn-sm me-1" data-bs-toggle="modal" data-bs-target="#viewModal{{ $cleaner->id }}" data-bs-toggle="tooltip" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        
                        <button type="button" class="btn btn-pastel-edit btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $cleaner->id }}" data-bs-toggle="tooltip" title="Edit Cleaner">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        
                        <button type="button" class="btn btn-pastel-delete btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cleaner->id }}" data-bs-toggle="tooltip" title="Delete Cleaner">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{ $cleaner->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $cleaner->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel{{ $cleaner->id }}">Cleaner Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Profile Picture at Top Center -->
                                <div class="text-center mb-4">
                                    @if($cleaner->profile_pic)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" 
                                             alt="Profile Picture" class="profile-picture img-fluid rounded-circle">
                                    @else
                                        <img src="{{ asset('images/placeholder.png') }}" alt="No Image" class="profile-picture img-fluid rounded-circle">
                                    @endif
                                </div>
                                <!-- Cleaner Details -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ $cleaner->cleaner_name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Username:</strong> {{ $cleaner->cleaner_username }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Phone Number:</strong> {{ $cleaner->cleaner_phoneNo }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong> 
                                            @if($cleaner->status == 'available')
                                                <span class="badge bg-success">{{ ucfirst($cleaner->status) }}</span>
                                            @elseif($cleaner->status == 'unavailable')
                                                <span class="badge bg-danger">{{ ucfirst($cleaner->status) }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($cleaner->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p><strong>Building:</strong> {{ $cleaner->building ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- Optionally add Edit button here -->
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $cleaner->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $cleaner->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $cleaner->id }}">Edit Cleaner - ID: {{ $cleaner->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form Fields -->
                                    <div class="mb-4">
                                        <label for="cleaner_name{{ $cleaner->id }}" class="form-label">Name</label>
                                        <input type="text" name="cleaner_name" id="cleaner_name{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_name }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="cleaner_phoneNo{{ $cleaner->id }}" class="form-label">Phone Number</label>
                                        <input type="text" name="cleaner_phoneNo" id="cleaner_phoneNo{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_phoneNo }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="cleaner_username{{ $cleaner->id }}" class="form-label">Username</label>
                                        <input type="text" name="cleaner_username" id="cleaner_username{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_username }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="status{{ $cleaner->id }}" class="form-label">Status</label>
                                        <select name="status" id="status{{ $cleaner->id }}" class="form-select" required>
                                            <option value="available" {{ $cleaner->status == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="unavailable" {{ $cleaner->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="building{{ $cleaner->id }}" class="form-label">Building</label>
                                        <input type="text" name="building" id="building{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->building }}">
                                    </div>
                                    <div class="mb-4">
                                        <label for="profile_pic{{ $cleaner->id }}" class="form-label">Profile Picture</label>
                                        <input type="file" name="profile_pic" id="profile_pic{{ $cleaner->id }}" class="form-control" accept="image/*">
                                        @if($cleaner->profile_pic)
                                            <div class="mt-2">
                                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" 
                                                     alt="Profile Picture" class="img-thumbnail rounded-circle" width="100" height="100">
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Add more fields as needed -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $cleaner->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $cleaner->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $cleaner->id }}">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this cleaner?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-pastel-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination with Page Size Selector -->
        <div class="d-flex justify-content-between align-items-center">
            <!-- Page Size Selector -->
            <form method="GET" action="{{ route('admin.cleaners') }}" class="mb-3">
                <div class="input-group">
                    <label class="input-group-text" for="per_page">Show</label>
                    <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </form>

            <!-- Pagination Links -->
            <div>
                {{ $cleaners->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection