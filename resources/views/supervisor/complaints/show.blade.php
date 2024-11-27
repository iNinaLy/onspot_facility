<x-app-layout>
    <div class="container my-5">
        <!-- Breadcrumb with SVG Back Icon -->
        <div class="d-flex align-items-center mb-4">
            <!-- Back Button with SVG Image -->
            <a href="{{ route('supervisor.complaints.index') }}" class="me-2 d-flex align-items-center text-decoration-none" style="color: #2E5675; padding: 0.375rem;">
                <!-- SVG Image for Back Button -->
                <img src="{{ asset('img/svg/back-arrow.svg') }}" alt="Back" style="width: 24px; height: 24px;">
            </a>
            <h6 class="text-muted m-0">Complaints / Details</h6>
        </div>

        <!-- Messages Section -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row gx-5">
            <!-- Image Section -->
            <div class="col-lg-6">
                <div class="image-section bg-light rounded-3 shadow-sm p-3 d-flex align-items-center justify-content-center" style="height: 300px;">
                    @if($complaint->getFirstMediaUrl('complaint_images'))
                        <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" alt="Complaint Image" class="img-fluid rounded-3" style="max-height: 100%; object-fit: cover;">
                    @else
                        <div class="placeholder-image text-muted">No Image Available</div>
                    @endif
                </div>
            </div>

            <!-- Details Section -->
            <div class="col-lg-6">
                <div class="details-section bg-white rounded-3 shadow-sm p-4">
                    <h4 class="mb-3 fw-bold text-primary">{{ $complaint->comp_desc }}</h4>
                    <ul class="list-unstyled mb-3 text-secondary">
                        <li><strong>Location:</strong> {{ $complaint->comp_location }}</li>
                        <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</li>
                        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($complaint->comp_time)->format('h:i A') }}</li>
                        <li><strong>Status:</strong> 
                            <span class="badge rounded-pill 
                                         @if($complaint->comp_status == 'completed') status-completed 
                                         @elseif($complaint->comp_status == 'ongoing') status-ongoing 
                                         @else status-pending @endif">
                                {{ ucfirst($complaint->comp_status) }}
                            </span>
                        </li>
                    </ul>

                    <div class="bg-light p-3 rounded-3 mb-3 shadow-sm">
                        <textarea class="form-control bg-transparent border-0" rows="3" readonly>{{ $complaint->comp_desc }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->role == 'supervisor')
            <div class="assign-section mt-5 p-4 bg-white rounded-3 shadow-sm">
                <h4 class="mb-4 text-primary">Assign Cleaners</h4>
                
                @if ($complaint->comp_status == 'pending')
                    <form action="{{ route('supervisor.assign.cleaner', ['id' => $complaint->id]) }}" method="POST">
                        @csrf
                        <!-- Number of Cleaners Selection -->
                        <div class="d-flex gap-3 mb-4 align-items-center">
                            <label for="no_of_cleaners" class="form-label mb-0 fw-semibold text-secondary">Number of Cleaners:</label>
                            <select class="form-select w-25 shadow-sm" name="no_of_cleaners" id="no_of_cleaners" required style="border-radius: 8px;">
                                <option selected disabled>Number of cleaners</option>
                                @for ($i = 1; $i <= $availableCleaners->count(); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="button" id="proceed-button" class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cleanerModal">Select Cleaners</button>
                        </div>

                        <!-- Modal for Cleaner Selection -->
                        <div class="modal fade" id="cleanerModal" tabindex="-1" aria-labelledby="cleanerModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cleanerModalLabel">Select Cleaners</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="cleaner-list list-unstyled" id="cleaner-grid">
                                            @foreach ($availableCleaners as $cleaner)
                                                <li class="cleaner-item d-flex align-items-center justify-content-between p-3 mb-2 shadow-sm rounded-3" style="background-color: #f7f9fc;">
                                                    <div class="d-flex align-items-center">
                                                        @if($cleaner->getFirstMediaUrl('profile_pictures'))
                                                            <img src="{{ $cleaner->getFirstMediaUrl('profile_pictures') }}" alt="Profile Picture" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                                                {{ strtoupper(substr($cleaner->cleaner_name, 0, 2)) }}
                                                            </div>
                                                        @endif
                                                        <span class="text-secondary fw-semibold">{{ $cleaner->cleaner_name }}</span>
                                                    </div>
                                                    <div class="checkbox-wrapper-39">
                                                        <label>
                                                            <input type="checkbox" name="cleaners[]" value="{{ $cleaner->id }}" id="cleaner-{{ $cleaner->id }}">
                                                            <span class="checkbox"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light shadow-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4">Assign Selected</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info mt-3 rounded-3 shadow-sm">
                        Complaint is already <strong>{{ ucfirst($complaint->comp_status) }}</strong>.
                    </div>
                    <h5 class="mt-4">Assigned Cleaners</h5>
                    <ul class="list-group list-group-flush rounded-3 shadow-sm">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $cleaner->cleaner_name }} 
                                <span class="text-muted small">Assigned by: {{ \App\Models\User::find($cleaner->pivot->assigned_by)->name ?? 'N/A' }},
                                Date: {{ \Carbon\Carbon::parse($cleaner->pivot->assigned_date)->format('d M Y h:i A') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No cleaners assigned.</li>
                        @endforelse
                    </ul>
                @endif
            </div>
        @else
            <div class="alert alert-warning mt-5 rounded-3 shadow-sm">
                You do not have permission to assign cleaners. Only supervisors can assign cleaners.
            </div>
        @endif
    </div>

    <!-- Custom Styles for Status Badge, Buttons, and Checkboxes -->
    <style>
        /* Status Badge Styles */
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 9999px;
            margin-top: 0.5rem;
        }

        .status-pending {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-ongoing {
            background-color: #fef3c7;
            color: #ca8a04;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Checkbox Styles */
        .checkbox-wrapper-39 label {
            display: block;
            width: 25px;
            height: 25px;
            cursor: pointer;
        }

        .checkbox-wrapper-39 input {
            visibility: hidden;
            display: none;
        }

        .checkbox-wrapper-39 input:checked ~ .checkbox {
            transform: rotate(45deg);
            width: 12px;
            margin-left: 8px;
            border-color: #24c78e;
            border-top-color: transparent;
            border-left-color: transparent;
            border-radius: 0;
        }

        .checkbox-wrapper-39 .checkbox {
            display: block;
            width: 100%;
            height: 100%;
            border: 2px solid #434343;
            border-radius: 4px;
            transition: all 0.375s;
        }

        /* Button Styles */
        .btn-primary {
            color: #fff;
            background-color: #374e66;
            border-color: #e7eaed;
        }

        .btn-primary:hover {
            background-color: #2e5675;
            border-color: #2e5675;
        }

        .bg-primary {
            background-color: #2c5472 !important;
            margin-right: 10px;
        }

        .text-primary {
            color: #1f2832 !important;
        }
    </style>
</x-app-layout>