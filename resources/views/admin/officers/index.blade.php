@extends('layouts.admin')

@section('title', 'Manage Officers')

@push('styles')
    <link href="{{ asset('resources/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('resources/admin/complaint.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom-table-modal.css') }}" rel="stylesheet" />

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
            font-size: 0.8rem;
            box-shadow: 0 0 8px rgba(169, 169, 169, 0.61);
            transition: transform 0.3s var(--transition-ease);
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
        <table class="table table-hover text-center" >
            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Building</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($officers as $officer)
                <tr>
                    <!-- Profile Picture Column -->
                    <td>
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
                           style="border-radius:12px"
                           title="Edit Officer">
                            <i class="bi bi-pencil-square" ></i>
                        </a>

                        <button type="button" 
                                class="btn btn-pastel-delete btn-sm" 
                                style="border-radius:12px"
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
                </div>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                {{ $officers->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
