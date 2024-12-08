@extends('layouts.admin')

@section('title', 'Manage Officers')

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

    /* Search Input */
    .search-input {
        width: 100%;
        padding-right: 30px;
        border-radius: 9999px;
        padding-left: 16px;
        background: #f0f4f8;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        box-shadow: 0 0 0 4px var(--primary-color);
        background: white;
    }

    .search-icon-container {
        position: absolute;
        right: 0;
        padding-right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }

    /* Profile picture adjustments */
    .w-10 {
        width: 2.5rem;
        height: 2.5rem;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        h1 {
            font-size: 1.5rem;
        }
        .table-auto {
            font-size: 0.9rem;
        }
        .w-10 {
            width: 2rem;
            height: 2rem;
        }
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
    // When input is cleared, display back all results
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function () {
        if (searchInput.value === '') {
            window.location.href = '{{ route('admin.officers') }}';
        }
    });

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
    <div class="heading text-center mb-4">
        <h1 class="header-title">Manage Officers</h1>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.officers') }}" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-6 position-relative">
                <input 
                    type="search" 
                    name="search"
                    class="search-input form-control" 
                    placeholder="Search by name, phone number, or email" 
                    value="{{ request()->query('search') }}"
                    id="search-input"
                    autocomplete="off"
                >
                <button type="submit" class="search-icon-container">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Officers Table -->
    <div class="table-responsive">
        @if($officers->isEmpty())
            <div class="alert alert-info text-center">No officers found.</div>
        @else
        <table class="table table-hover">
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
                            <img src="{{ asset('storage/' . $officer->profile_pic) }}" alt="Profile Picture" class="w-10 h-10 rounded-circle border-2 border-gray-300 object-cover">
                        @else
                            <span class="text-gray-500">No Image</span>
                        @endif
                    </td>

                    <!-- Officer Details -->
                    <td>{{ $officer->name ?? 'Not Available' }}</td>
                    <td>{{ $officer->email ?? 'Not Available' }}</td>
                    <td>{{ $officer->phone_no ?? 'Not Available' }}</td>
                    <td>{{ $officer->building ?? 'Not Set' }}</td>

                    <!-- Actions -->
                    <td>
                        <!-- Action Buttons with Pastel Colors -->
                        <button type="button" class="btn btn-pastel-view btn-sm me-1" data-bs-toggle="modal" data-bs-target="#viewModal{{ $officer->id }}" data-bs-toggle="tooltip" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        
                        <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn btn-pastel-edit btn-sm me-1" data-bs-toggle="tooltip" title="Edit Officer">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        
                        <button 
                            type="button" 
                            class="btn btn-pastel-delete btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal{{ $officer->id }}"
                            data-bs-toggle="tooltip"
                            title="Delete Officer"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{ $officer->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $officer->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel{{ $officer->id }}">Officer Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Profile Picture at Top Center -->
                                <div class="text-center mb-4">
                                    @if ($officer->profile_pic)
                                        <img src="{{ asset('storage/' . $officer->profile_pic) }}" alt="Profile Picture" class="profile-picture img-fluid rounded-circle">
                                    @else
                                        <img src="{{ asset('images/placeholder.png') }}" alt="No Image" class="profile-picture img-fluid rounded-circle">
                                    @endif
                                </div>
                                <!-- Officer Details -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ $officer->name ?? 'Not Available' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> {{ $officer->email ?? 'Not Available' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Phone Number:</strong> {{ $officer->phone_no ?? 'Not Available' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Building:</strong> {{ $officer->building ?? 'Not Set' }}</p>
                                    </div>
                                </div>
                                <!-- Add more details if necessary -->
                            </div>
                            <div class="modal-footer">
                                <!-- Optionally add Edit button here -->
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
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
                                    <button type="submit" class="btn btn-pastel-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of modal -->
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-between align-items-center">
            <!-- Pagination Links -->
            <div>
                {{ $officers->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
