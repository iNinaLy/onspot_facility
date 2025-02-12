@extends('layouts.admin')

@section('title', 'Manage Officers')

@push('styles')
    <link href="{{ asset('resources/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('resources/admin/complaint.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom-table-modal.css') }}" rel="stylesheet" />

    <style>
        /* Enhanced Profile Picture Styling */
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(169, 169, 169, 0.61);
            transition: transform 0.3s ease;
        }
        .profile-pic:hover {
            transform: scale(1.1);
        }
        /* Center profile picture container */
        .profile-pic-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Header Styling */
        .header-container {
            margin-top: 2rem;  /* extra space before title */
            text-align: center;
        }
        .header-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        /* Search Input Enhancements */
        .search-container {
            margin: 2rem auto;  /* spacing above and below search bar */
            max-width: 400px;
        }
        .input-group.rounded-pill .form-control {
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
            border-right: none;
        }
        .input-group.rounded-pill .btn {
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
        }

        /* Table Enhancements */
        .table-responsive {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Action Buttons Enhancements */
        .btn-pastel-edit {
            background-color: #a1d99b;
            border: none;
            color: #fff;
        }
        .btn-pastel-edit:hover {
            background-color: #82c584;
        }
        .btn-pastel-delete {
            background-color: #fca5a5;
            border: none;
            color: #fff;
        }
        .btn-pastel-delete:hover {
            background-color: #f87171;
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite([
        'resources/admin/app.js',
        'resources/admin/dashboard.js',
        'resources/admin/complaint.js',
    ])
@endpush

@section('content')
<div class="container my-5">
    <!-- Header Section -->
    <div class="header-container">
        <h1 class="header-title">Manage Officers</h1>
    </div>

    <!-- Centered Search Bar -->
    <div class="search-container">
        <form method="GET" action="{{ route('admin.officers') }}">
            <div class="input-group rounded-pill">
                <input 
                    type="search" 
                    name="search"
                    class="form-control" 
                    placeholder="Search by name, phone number, or email" 
                    value="{{ request()->query('search') }}"
                    autocomplete="off"
                >
                <button class="btn btn-outline-secondary" type="submit" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

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
            <div class="alert alert-info text-center p-3">No officers found.</div>
        @else
        <table class="table table-hover text-center m-0">
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
                    <td class="profile-pic-container">
                        @if ($officer->profile_pic)
                            <img src="data:image/jpeg;base64,{{ base64_encode($officer->profile_pic) }}"
                                 alt="{{ $officer->name }}" 
                                 class="profile-pic"
                                 onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                        @else
                            <img src="{{ asset('images/default-image.jpeg') }}"
                                 alt="Default Image"
                                 class="profile-pic">
                        @endif
                    </td>
                    <!-- Officer Details -->
                    <td>{{ $officer->name ?? 'Not Available' }}</td>
                    <td>{{ $officer->email ?? 'Not Available' }}</td>
                    <td>{{ $officer->phone_no ?? 'Not Available' }}</td>
                    <td>{{ $officer->building ?? 'Not Set' }}</td>
                    <!-- Actions -->
                    <td>
                        <a href="{{ route('admin.officers.edit', $officer->id) }}" 
                           class="btn btn-pastel-edit btn-sm me-1" 
                           style="border-radius: 10px;" title="Edit Officer">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-pastel-delete btn-sm" 
                                style="border-radius: 10px;" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal{{ $officer->id }}" 
                                title="Delete {{ $officer->name }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $officer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $officer->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="deleteModalLabel{{ $officer->id }}">Confirmation</h4>
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
                </div>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $officers->appends(request()->query())->links() }}
    </div>
</div>
@endsection
