@extends('layouts.admin')

@section('title', 'Manage Cleaners')

@push('styles')

<!-- Include Bootstrap CSS (if not already included in your layout) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Include Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<style>
    /* ==== Color Palette ==== */
    :root {
        --primary-color: #2e5675; /* Deep Blue */
        --secondary-color: #f0f0f0; /* Pastel Grey */
        --accent-color: #a8dadc; /* Soft Teal */
        --text-color: #333333; /* Dark Grey */
        --background-color: #ffffff; /* White */
        --card-background: #f9f9f9; /* Light Grey for Cards */
        --border-color: #e0e0e0; /* Subtle Grey Border */
        --button-hover: #1b3a5f; /* Darker Blue for Hover */
        /* ==== Pastel Button Colors ==== */
        --pastel-view: #ebebeb; /* Light Grey */
        --pastel-edit: #ebebeb; /* Light Grey */
        --pastel-delete: #dd5858; /* Soft Red */
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                     Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .heading {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 2rem;
    }

    /* ==== General Styles ==== */
    .container {
        max-width: 1200px;
    }

    /* ==== Metrics Cards ==== */
    .metrics-card {
        background-color: var(--card-background);
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px rgba(46, 86, 117, 0.1);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        padding: 1rem;
    }

    .metrics-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 8px rgba(46, 86, 117, 0.15);
    }

    .metrics-card .card-title {
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    .metrics-card .card-text {
        font-size: 2rem;
        font-weight: bold;
        color: var(--text-color);
    }

    /* ==== Buttons ==== */
    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        color: #ffffff;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: var(--button-hover);
    }

    /* ==== Custom Pastel Buttons ==== */
    .btn-pastel-view {
        background-color: var(--pastel-view);
        border: none;
        color: #000000;
        border-radius: 8px;
        transition: background-color 0.2s;
    }

    .btn-pastel-view:hover {
        background-color: #d6d6d6;
    }

    .btn-pastel-edit {
        background-color: var(--pastel-edit);
        border: none;
        color: #000000;
        border-radius: 8px;
        transition: background-color 0.2s;
    }

    .btn-pastel-edit:hover {
        background-color: #d6d6d6;
    }

    .btn-pastel-delete {
        background-color: var(--pastel-delete);
        border: none;
        color: #ffffff;
        border-radius: 8px;
        transition: background-color 0.2s;
    }

    .btn-pastel-delete:hover {
        background-color: #c74747;
    }

    /* ==== Table Styles ==== */
    table {
        background-color: var(--background-color);
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    table thead tr th {
        background-color: var(--secondary-color);
        color: var(--text-color);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1rem;
        font-weight: 600;
        font-size: 1rem;
        border-bottom: none;
    }

    table tbody tr td {
        background-color: var(--background-color);
        padding: 1rem;
        border-bottom: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 8px;
    }

    /* Hover Effect for Table Rows */
    table tbody tr:hover td {
        background-color: #ebebeb;
        cursor: pointer;
    }

    /* ==== Modal Styles ==== */
    .modal-content {
        background-color: var(--background-color);
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(46, 86, 117, 0.2);
        padding: 1.5rem;
    }

    .modal-header {
        border-bottom: none;
        text-align: center;
    }

    .modal-title {
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: bold;
    }

    .modal-body {
        padding-top: 1rem;
    }

    .modal-footer {
        border-top: none;
        justify-content: center;
    }

    /* Centered Image in Modal */
    .modal-body .profile-picture {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        display: block;
    }

    /* ==== Tooltips ==== */
    .tooltip-inner {
        background-color: var(--primary-color);
        color: #ffffff;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    /* ==== Pagination ==== */
    .pagination .page-link {
        background-color: var(--background-color);
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin: 0 2px;
    }

    .pagination .page-link:hover {
        background-color: var(--primary-color);
        color: #ffffff;
    }

    .pagination .active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
    }

    /* ==== Toastr Notifications ==== */
    #toast-container > .toast-success {
        background-color: #4caf50;
    }

    #toast-container > .toast-error {
        background-color: #f44336;
    }
</style>
@endpush

@push('scripts')
<!-- Include jQuery (if not already included in your layout) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap JS (if not already included in your layout) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Toastr JS -->
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
