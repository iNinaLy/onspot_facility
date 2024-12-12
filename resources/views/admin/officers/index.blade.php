@extends('layouts.admin')

@section('title', 'Manage Officers')

@push('styles')
    <link href="resources/admin/app.css" rel="stylesheet" />
    <link href="resources/admin/complaint.css" rel="stylesheet" />
@endpush

@push('scripts')
    @vite(['resources/js/app.js'])
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
            <div class="col-md-6">
                <div class="input-group rounded-pill">
                    <input 
                        type="search" 
                        name="search"
                        class="form-control" 
                        placeholder="Search by name, phone number, or email" 
                        value="{{ request()->query('search') }}"
                        id="search-input"
                        autocomplete="off"
                    >
                    <button 
                        class="btn btn-outline-secondary" 
                        type="submit" 
                        id="search-button" 
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