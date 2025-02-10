@extends('layouts.admin')

@section('title', 'Manage Cleaners')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="{{ asset('resources/admin/app.css') }}" rel="stylesheet">

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
        
        .modal-profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            object-fit: cover;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: none;
            font-size: 1rem;
            text-align: center;
            box-shadow: 0 0 8px rgb(172, 202, 208);
            transition: transform 0.3s var(--transition-ease);
        }

        .badge-available {
            background-color: #a0cede;
            color: #182233;
            padding: 0.35em 0.65em;
            border-radius: 4rem;
            font-size: 0.8em;
        }

        .badge-unavailable {
            background-color: #ffbebe;
            color: #731111;
            padding: 0.35em 0.65em;
            border-radius: 4rem;
            font-size: 0.8rem;
        }

        /* Override to prevent backdrop blur */
        .modal-backdrop,
        .modal,
        body.modal-open {
            backdrop-filter: none !important;
            filter: none !important;
        }

        /* Custom Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            padding: 1.5rem 0;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .pagination .page-item {
            list-style: none;
        }
        .pagination .page-link {
            display: block;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            color: #2e3a59;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination .page-link:hover {
            background-color: #2e3a59;
            color: #fff;
        }
        .pagination .active .page-link {
            background-color: #2e3a59;
            color: #fff;
            border-color: #2e3a59;
        }
        .pagination .disabled .page-link {
            color: #ccc;
            pointer-events: none;
            background-color: #f2f4f8;
            border-color: #ccc;
        }
    </style>
@endpush

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="heading mb-4">
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

    <form method="GET" action="{{ route('admin.cleaners') }}" class="mb-4">
    <div class="row g-2" style="justify-content: flex-end;">
        <!-- Narrower search input -->
        <div class="col-md-4" >
            <input type="text" name="search" class="form-control" style="border-radius:16px;" placeholder="Search cleaners..." value="{{ request('search') }}">
        </div>
        <!-- Smaller search button using btn-sm -->
        <div class="col-md-2" style="width: 7%;">
            <button class="btn btn-primary btn-sm" type="submit" style="border-radius:16px;">Search</button>
        </div>
    </div>
</form>


    <!-- Tabs for Cleaner Status -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#available" role="tab">Available</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#unavailable" role="tab">Unavailable</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Available Cleaners Tab -->
        <div class="tab-pane fade show active" id="available" role="tabpanel">
            @if($availableCleanersList->isEmpty())
                <div class="alert alert-info text-center">No available cleaners found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Building</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableCleanersList as $cleaner)
                            <tr>
                                <td>{{ $cleaner->id }}</td>
                                <td>
                                    @if($cleaner->profile_pic)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                             alt="{{ $cleaner->cleaner_name }}" 
                                             class="profile-pic"
                                             onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                                    @else
                                        <img src="{{ asset('images/default-image.png') }}"
                                             alt="Default Image"
                                             class="profile-pic">
                                    @endif
                                </td>
                                <td>{{ $cleaner->cleaner_name }}</td>
                                <td>{{ $cleaner->cleaner_phoneNo }}</td>
                                <td>{{ $cleaner->cleaner_username }}</td>
                                <td>
                                    <span class="badge-available">{{ ucfirst($cleaner->status) }}</span>
                                </td>
                                <td>{{ $cleaner->building ?? 'N/A' }}</td>
                                <td>
                                    <span data-bs-toggle="tooltip" title="Edit Cleaner">
                                        <button type="button" 
                                            class="btn btn-pastel-edit btn-sm me-1"
                                            style="border-radius:12px"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $cleaner->id }}"
                                            title="Edit Cleaner">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </span>

                                    <span data-bs-toggle="tooltip" title="Delete Cleaner">
                                        <button type="button" 
                                            class="btn btn-pastel-delete btn-sm" 
                                            style="border-radius:12px"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $cleaner->id }}"
                                            title="Delete Cleaner">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $cleaner->id }}" tabindex="-1"
                                aria-labelledby="editModalLabel{{ $cleaner->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $cleaner->id }}">Edit Details</h5>
                                                <button type="button" class="btn-close" style="background-color:#fff" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                        
                                            @if($cleaner->profile_pic)
                                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                                    alt="{{ $cleaner->cleaner_name }}"
                                                    class="modal-profile-pic"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                                            @else
                                                <img src="{{ asset('images/default-image.png') }}"
                                                    alt="Profile picture"
                                                    class="modal-profile-pic">
                                            @endif
                                            
                                            <div class="modal-body">
                                                <!-- Cleaner Name -->
                                                <div class="mb-4">
                                                    <label for="cleaner_name{{ $cleaner->id }}" class="form-label">Name</label>
                                                    <input type="text" name="cleaner_name" id="cleaner_name{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_name }}" required>
                                                </div>

                                                <!-- Cleaner Phone Number -->
                                                <div class="mb-4">
                                                    <label for="phone_no{{ $cleaner->id }}" class="form-label">Phone Number</label>
                                                    <input type="text" name="phone_no" id="phone_no{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_phoneNo }}" required>
                                                </div>

                                                <!-- Cleaner Username -->
                                                <div class="mb-4">
                                                    <label for="username{{ $cleaner->id }}" class="form-label">Username</label>
                                                    <input type="text" name="username" id="username{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_username }}" required>
                                                </div>

                                                <!-- Status -->
                                                <div class="mb-4">
                                                    <label for="status{{ $cleaner->id }}" class="form-label">Status</label>
                                                    <select name="status" id="status{{ $cleaner->id }}" class="form-select" required>
                                                        <option value="available" {{ $cleaner->status == 'available' ? 'selected' : '' }}>Available</option>
                                                        <option value="unavailable" {{ $cleaner->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                                    </select>
                                                </div>

                                                <!-- Building -->
                                                <div class="mb-4">
                                                    <label for="building{{ $cleaner->id }}" class="form-label">Building</label>
                                                    <select name="building" id="building{{ $cleaner->id }}" class="form-select" required>
                                                        <option value="Building A" {{ $cleaner->building == 'Building A' ? 'selected' : '' }}>Building A</option>
                                                        <option value="Building B" {{ $cleaner->building == 'Building B' ? 'selected' : '' }}>Building B</option>
                                                        <option value="Building C" {{ $cleaner->building == 'Building C' ? 'selected' : '' }}>Building C</option>
                                                    </select>
                                                </div>

                                                <!-- Profile Picture -->
                                                <div class="mb-4">
                                                    <label for="profile_pic{{ $cleaner->id }}" class="form-label">Profile Picture</label>
                                                    <input type="file" name="profile_pic" id="profile_pic{{ $cleaner->id }}" class="form-control" accept="image/*">
                                                </div>

                                                <!-- RESET PASSWORD BUTTON - triggers static modal -->
                                                <div class="mb-4">
                                                    <button type="button" class="btn btn-danger" style="border-radius:16px; background-color:rgb(206, 77, 77); margin-top:3rem;" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                                        Reset Password
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn" style="background-color:#ececec; border-radius:16px;" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" style="border-radius:16px;">Save Changes</button>
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
                                            <button type="button" class="btn" style="background-color:#ececec; border-radius:16px;" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-pastel-delete" style="border-radius:16px;">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Delete Confirmation Modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Available Cleaners -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $availableCleanersList->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Unavailable Cleaners Tab -->
        <div class="tab-pane fade" id="unavailable" role="tabpanel">
            @if($unavailableCleanersList->isEmpty())
                <div class="alert alert-info text-center">No unavailable cleaners found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Building</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unavailableCleanersList as $cleaner)
                            <tr>
                                <td>{{ $cleaner->id }}</td>
                                <td>
                                    @if($cleaner->profile_pic)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                             alt="{{ $cleaner->cleaner_name }}" 
                                             class="profile-pic"
                                             onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                                    @else
                                        <img src="{{ asset('images/default-image.png') }}"
                                             alt="Default Image"
                                             class="profile-pic">
                                    @endif
                                </td>
                                <td>{{ $cleaner->cleaner_name }}</td>
                                <td>{{ $cleaner->cleaner_phoneNo }}</td>
                                <td>{{ $cleaner->cleaner_username }}</td>
                                <td>
                                    <span class="badge-unavailable">{{ ucfirst($cleaner->status) }}</span>
                                </td>
                                <td>{{ $cleaner->building ?? 'N/A' }}</td>
                                <td>
                                    <span data-bs-toggle="tooltip" title="Edit Cleaner">
                                        <button type="button" 
                                            class="btn btn-pastel-edit btn-sm me-1"
                                            style="border-radius:12px"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $cleaner->id }}"
                                            title="Edit Cleaner">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </span>

                                    <span data-bs-toggle="tooltip" title="Delete Cleaner">
                                        <button type="button" 
                                            class="btn btn-pastel-delete btn-sm" 
                                            style="border-radius:12px"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $cleaner->id }}"
                                            title="Delete Cleaner">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr>

                            <!-- Edit Modal (Reuse same markup as above) -->
                            <div class="modal fade" id="editModal{{ $cleaner->id }}" tabindex="-1"
                                aria-labelledby="editModalLabel{{ $cleaner->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $cleaner->id }}">Edit Details</h5>
                                                <button type="button" class="btn-close" style="background-color:#fff" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                        
                                            @if($cleaner->profile_pic)
                                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                                    alt="{{ $cleaner->cleaner_name }}"
                                                    class="modal-profile-pic"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                                            @else
                                                <img src="{{ asset('images/default-image.png') }}"
                                                    alt="Profile picture"
                                                    class="modal-profile-pic">
                                            @endif
                                            
                                            <div class="modal-body">
                                                <!-- Cleaner Name -->
                                                <div class="mb-4">
                                                    <label for="cleaner_name{{ $cleaner->id }}" class="form-label">Name</label>
                                                    <input type="text" name="cleaner_name" id="cleaner_name{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_name }}" required>
                                                </div>

                                                <!-- Cleaner Phone Number -->
                                                <div class="mb-4">
                                                    <label for="phone_no{{ $cleaner->id }}" class="form-label">Phone Number</label>
                                                    <input type="text" name="phone_no" id="phone_no{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_phoneNo }}" required>
                                                </div>

                                                <!-- Cleaner Username -->
                                                <div class="mb-4">
                                                    <label for="username{{ $cleaner->id }}" class="form-label">Username</label>
                                                    <input type="text" name="username" id="username{{ $cleaner->id }}" class="form-control" value="{{ $cleaner->cleaner_username }}" required>
                                                </div>

                                                <!-- Status -->
                                                <div class="mb-4">
                                                    <label for="status{{ $cleaner->id }}" class="form-label">Status</label>
                                                    <select name="status" id="status{{ $cleaner->id }}" class="form-select" required>
                                                        <option value="available" {{ $cleaner->status == 'available' ? 'selected' : '' }}>Available</option>
                                                        <option value="unavailable" {{ $cleaner->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                                    </select>
                                                </div>

                                                <!-- Building -->
                                                <div class="mb-4">
                                                    <label for="building{{ $cleaner->id }}" class="form-label">Building</label>
                                                    <select name="building" id="building{{ $cleaner->id }}" class="form-select" required>
                                                        <option value="Building A" {{ $cleaner->building == 'Building A' ? 'selected' : '' }}>Building A</option>
                                                        <option value="Building B" {{ $cleaner->building == 'Building B' ? 'selected' : '' }}>Building B</option>
                                                        <option value="Building C" {{ $cleaner->building == 'Building C' ? 'selected' : '' }}>Building C</option>
                                                    </select>
                                                </div>

                                                <!-- Profile Picture -->
                                                <div class="mb-4">
                                                    <label for="profile_pic{{ $cleaner->id }}" class="form-label">Profile Picture</label>
                                                    <input type="file" name="profile_pic" id="profile_pic{{ $cleaner->id }}" class="form-control" accept="image/*">
                                                </div>

                                                <!-- RESET PASSWORD BUTTON - triggers static modal -->
                                                <div class="mb-4">
                                                    <button type="button" class="btn btn-danger" style="border-radius:16px; background-color:rgb(206, 77, 77); margin-top:3rem;" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                                        Reset Password
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn" style="background-color:#ececec; border-radius:16px;" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" style="border-radius:16px;">Save Changes</button>
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
                                            <button type="button" class="btn" style="background-color:#ececec; border-radius:16px;" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-pastel-delete" style="border-radius:16px;">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Delete Confirmation Modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Unavailable Cleaners -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $unavailableCleanersList->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Static Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Using the reset password route with a placeholder id (0) -->
                <form action="{{ route('admin.supervisors.resetPassword', ['id' => 0]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <!-- New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password" class="form-control" id="new_password" placeholder="New Password" required>
                        <label for="new_password">New Password</label>
                        <span class="toggle-password" data-target="new_password" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        <small id="passwordFeedback" class="form-text text-danger d-none">
                            Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a digit, and a special character.
                        </small>
                    </div>

                    <!-- Confirm New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" placeholder="Confirm Password" required>
                        <label for="new_password_confirmation">Confirm Password</label>
                        <span class="toggle-password" data-target="new_password_confirmation" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        <small id="passwordMatchError" class="text-danger d-none">Passwords do not match.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if (session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        {{ session('status') }}
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
            <span class="text-green-500">&times;</span>
        </button>
    </div>
@endif
@endsection

@push('scripts')
    @vite([
        'resources/admin/app.js',
        'resources/admin/dashboard.js',
        'resources/admin/complaint.js',
    ])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Toastr Notifications
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Toggle Password Visibility
            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (targetInput.getAttribute('type') === 'password') {
                        targetInput.setAttribute('type', 'text');
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    } else {
                        targetInput.setAttribute('type', 'password');
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    }
                });
            });

            // Password Validation for Reset Password Modal
            const passwordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const passwordMatchError = document.getElementById('passwordMatchError');

            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

            function validatePassword() {
                const isPasswordValid = passwordPattern.test(passwordInput.value);
                const doPasswordsMatch = passwordInput.value === confirmPasswordInput.value;

                passwordFeedback.classList.toggle('d-none', isPasswordValid);
                passwordMatchError.classList.toggle('d-none', doPasswordsMatch || confirmPasswordInput.value === "");
            }

            if(passwordInput && confirmPasswordInput) {
                passwordInput.addEventListener('input', validatePassword);
                confirmPasswordInput.addEventListener('input', validatePassword);
            }

            // Clear lingering modal effects on close
            var resetPasswordModal = document.getElementById('resetPasswordModal');
            if(resetPasswordModal) {
                resetPasswordModal.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.filter = 'none';
                });
            }
        });
    </script>
@endpush
