@extends('layouts.admin')

@section('title', 'Manage Supervisors')

@push('styles')
    <style>
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
            transition: transform 0.3s var(--transition-ease);
        }
        
    </style>
@endpush

@push('scripts')
    @vite(['resources/admin/app.js', 'resources/admin/complaint.js'])
    <script>
        function openModal(modalId) {
            var modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            if(modal) {
                modal.style.display = 'none';
                modal.style.backdropFilter = 'none';
                modal.style.pointerEvents = 'none';
            }
        }
    </script>
@endpush

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="heading text-center mb-4">
        <h1 class="header-title">Manage Supervisors</h1>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.supervisors.index') }}" class="mb-4 search-form">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group rounded-pill enhanced-rounded-search">
                    <input 
                        type="search" 
                        name="search"
                        class="form-control search-input" 
                        placeholder="Search by name, phone number, or email" 
                        value="{{ request()->query('search') }}"
                        autocomplete="on"
                    >
                    <button 
                        class="btn btn-outline-secondary" 
                        type="submit" 
                        aria-label="Search"
                    >
                        <i class="bi bi-search"></i>
                    </button>
                </div>
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
                    <td>
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
                </div> <!-- End of modal -->
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-between align-items-center">
            <!-- Pagination Links -->
            <div>
                {{ $supervisors->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
