@extends('layouts.admin')

@section('title', 'Manage Supervisors')

@push('styles')
    <style>
        /* Enhanced Profile Picture Styling */
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            object-fit: cover;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: none;
            font-size: 0.8rem;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
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

        /* Header Section */
        .header-container {
            margin-top: 2rem;
            text-align: center;
        }
        .header-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        /* Search Container */
        .search-container {
            margin: 2rem auto;
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
    @vite(['resources/admin/app.js', 'resources/admin/complaint.js'])
    <script>
        function openModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                modal.style.backdropFilter = 'none';
                modal.style.pointerEvents = 'none';
            }
        }
    </script>
@endpush

@section('content')
<div class="container my-5">
    <!-- Header Section -->
    <div class="header-container">
        <h1 class="header-title">Manage Supervisors</h1>
    </div>

    <!-- Centered Search Bar -->
    <div class="search-container">
        <form method="GET" action="{{ route('admin.supervisors.index') }}">
            <div class="input-group rounded-pill">
                <input 
                    type="search" 
                    name="search"
                    class="form-control" 
                    placeholder="Search by name, phone number, or email" 
                    value="{{ request()->query('search') }}"
                    autocomplete="on"
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

    <!-- Supervisors Table -->
    <div class="table-responsive">
        @if($supervisors->isEmpty())
            <div class="alert alert-info text-center">No supervisors found.</div>
        @else
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisors as $supervisor)
                    <tr>
                        <!-- Profile Picture Column -->
                        <td class="profile-pic-container">
                            @if ($supervisor->profile_pic)
                                <img src="data:image/jpeg;base64,{{ base64_encode($supervisor->profile_pic) }}"
                                     alt="{{ $supervisor->name }}" 
                                     class="profile-pic"
                                     onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                            @else
                                <img src="{{ asset('images/default-image.jpeg') }}"
                                     alt="Default Image"
                                     class="profile-pic">
                            @endif
                        </td>

                        <!-- Supervisor Details -->
                        <td>{{ $supervisor->name ?? 'Not Available' }}</td>
                        <td>{{ $supervisor->email ?? 'Not Available' }}</td>
                        <td>{{ $supervisor->phone_no ?? 'Not Available' }}</td>

                        <!-- Actions -->
                        <td>
                            <a href="{{ route('admin.supervisors.edit', $supervisor->id) }}"
                               class="btn btn-pastel-edit btn-sm me-1" 
                               style="border-radius:12px;"
                               title="Edit Details">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-pastel-delete btn-sm" 
                                    style="border-radius:12px"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal{{ $supervisor->id }}"
                                    title="Delete {{ $supervisor->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

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
                                        <button type="submit" class="btn btn-pastel-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of Modal -->
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        {{ $supervisors->appends(request()->query())->links() }}
    </div>
</div>
@endsection
