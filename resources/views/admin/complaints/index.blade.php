@extends('layouts.admin')

@section('title', 'Manage Complaints')

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<!-- Custom Styles -->
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

    /* ==== Metrics Cards ==== */
    .metrics-card {
        background-color: var(--card-background);
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px rgba(46, 86, 117, 0.1);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
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

    /* ==== Action Buttons ==== */
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

    /* ==== Pastel Action Buttons ==== */
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

    /* ==== Select All Checkbox ==== */
    #select-all {
        transform: scale(1.2);
    }

    /* ==== Status Select Dropdown ==== */
    .status-select {
        width: 100%;
        border: none;
        background-color: transparent;
        padding: 0;
        font-size: inherit;
        color: inherit;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .status-select:focus {
        outline: none;
        box-shadow: none;
    }

    /* Loading Spinner */
    .status-select.loading {
        pointer-events: none;
        background-image: url('data:image/svg+xml;base64,{{ base64_encode('<svg xmlns="http://www.w3.org/2000/svg" style="margin:auto;background:none;display:block;" width="24px" height="24px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="#2e5675" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>') }}');
        background-repeat: no-repeat;
        background-position: right center;
        background-size: 1rem 1rem;
    }

    /* ==== Bulk Actions Toolbar ==== */
    .bulk-actions-toolbar {
        display: none;
        margin-bottom: 1rem;
    }

    .bulk-actions-toolbar.active {
        display: flex;
    }

    .bulk-actions-toolbar .btn {
        margin-right: 0.5rem;
    }

    /* ==== Page Size Selector Styles ==== */
    .page-size-selector {
        max-width: 120px;
    }

    .page-size-selector .form-select {
        width: 70px;
        display: inline-block;
    }

    /* ==== View Modal Styles ==== */
    .complaint-details {
        margin-bottom: 1rem;
    }

    .complaint-details h6 {
        font-weight: bold;
        color: var(--primary-color);
    }

    .complaint-details p {
        margin-bottom: 0.5rem;
    }

    .complaint-details .detail-label {
        font-weight: bold;
        color: var(--text-color);
    }

    .complaint-details .detail-value {
        color: var(--text-color);
    }

    .complaint-details hr {
        margin: 1rem 0;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- CSRF Token Meta -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    $(document).ready(function() {
        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Handle individual delete button click
        $('.delete-button').on('click', function() {
            var complaintId = $(this).data('complaint-id');
            $('#deleteComplaintId').val(complaintId);
            $('#deleteModal').modal('show');
        });

        // Confirm individual delete in modal
        $('#confirmDeleteComplaintButton').on('click', function() {
            var complaintId = $('#deleteComplaintId').val();
            var actionUrl = '{{ route("admin.complaints.bulkAction") }}';

            // Create a form dynamically
            var form = $('<form>', {
                'method': 'POST',
                'action': actionUrl
            });

            // Add CSRF token and action
            form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
            form.append('<input type="hidden" name="action" value="delete">');
            form.append('<input type="hidden" name="selected_complaints[]" value="' + complaintId + '">');

            $('body').append(form);
            form.submit();
        });

        // Handle bulk delete button click
        $('#bulkDeleteButton').on('click', function(e) {
            e.preventDefault();

            // Get selected complaint IDs
            var selectedComplaints = $('.select-box:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedComplaints.length === 0) {
                toastr.warning('Please select at least one complaint to delete.');
                return;
            }

            $('#bulkDeleteModal').modal('show');
        });

        // Confirm bulk delete in modal
        $('#confirmBulkDeleteButton').on('click', function() {
            var actionUrl = '{{ route("admin.complaints.bulkAction") }}';

            // Get selected complaint IDs
            var selectedComplaints = $('.select-box:checked').map(function() {
                return $(this).val();
            }).get();

            // Create a form dynamically
            var form = $('<form>', {
                'method': 'POST',
                'action': actionUrl
            });

            // Add CSRF token and action
            form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
            form.append('<input type="hidden" name="action" value="delete">');

            // Append selected complaints
            selectedComplaints.forEach(function(id) {
                form.append('<input type="hidden" name="selected_complaints[]" value="' + id + '">');
            });

            $('body').append(form);
            form.submit();
        });

        // Handle bulk mark as completed button click
        $('#bulkMarkCompletedButton').on('click', function(e) {
            e.preventDefault();

            // Get selected complaint IDs
            var selectedComplaints = $('.select-box:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedComplaints.length === 0) {
                toastr.warning('Please select at least one complaint to mark as completed.');
                return;
            }

            $('#bulkMarkCompletedModal').modal('show');
        });

        // Confirm bulk mark as completed in modal
        $('#confirmBulkMarkCompletedButton').on('click', function() {
            var actionUrl = '{{ route("admin.complaints.bulkAction") }}';

            // Get selected complaint IDs
            var selectedComplaints = $('.select-box:checked').map(function() {
                return $(this).val();
            }).get();

            // Create a form dynamically
            var form = $('<form>', {
                'method': 'POST',
                'action': actionUrl
            });

            // Add CSRF token and action
            form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
            form.append('<input type="hidden" name="action" value="mark_completed">');

            // Append selected complaints
            selectedComplaints.forEach(function(id) {
                form.append('<input type="hidden" name="selected_complaints[]" value="' + id + '">');
            });

            $('body').append(form);
            form.submit();
        });

        // Handle select all checkbox
        $('#select-all').on('click', function(){
            $('.select-box').prop('checked', this.checked);
            toggleBulkActions();
        });

        // Handle individual checkbox click
        $('.select-box').on('change', function() {
            toggleBulkActions();
        });

        // Toggle Bulk Actions Toolbar
        function toggleBulkActions() {
            var selectedCount = $('.select-box:checked').length;
            if (selectedCount > 0) {
                $('.bulk-actions-toolbar').addClass('active');
            } else {
                $('.bulk-actions-toolbar').removeClass('active');
            }
        }

        // Handle status change
        $('.status-select').on('change', function(e) {
            e.stopPropagation(); // Prevent the row click event
            var selectElement = $(this);
            var complaintId = selectElement.data('complaint-id');
            var newStatus = selectElement.val();
            var url = selectElement.data('url');

            // Add loading class and disable the select
            selectElement.addClass('loading').prop('disabled', true);

            // Send AJAX request to update status
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    comp_status: newStatus,
                    _token: csrfToken
                },
                success: function(response) {
                    selectElement.removeClass('loading').prop('disabled', false);
                    if(response.status != 'success') {
                        toastr.error(response.message || 'An error occurred while updating the status.');
                        // Optionally, revert the select to previous value
                        // location.reload();
                    } else {
                        toastr.success('Status updated successfully.');
                    }
                },
                error: function(xhr, status, error) {
                    selectElement.removeClass('loading').prop('disabled', false);
                    toastr.error('An error occurred while updating the status.');
                    // Optionally, revert the select to previous value
                    // location.reload();
                }
            });
        });

        // Toastr Notifications
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endpush

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="heading">
        <h1 class="header-title">Manage Complaints</h1>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-5">
        <!-- Total Complaints -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Total Complaints</h5>
                <p class="card-text">{{ $totalComplaints }}</p>
            </div>
        </div>
        <!-- Pending Complaints -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Pending</h5>
                <p class="card-text">{{ $pendingComplaints }}</p>
            </div>
        </div>
        <!-- Ongoing Complaints -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Ongoing</h5>
                <p class="card-text">{{ $ongoingComplaints }}</p>
            </div>
        </div>
        <!-- Completed Complaints -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Completed</h5>
                <p class="card-text">{{ $completedComplaints }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('admin.complaints') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search complaints..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <!-- Bulk Actions Toolbar -->
    <div class="bulk-actions-toolbar mb-3">
        <button type="button" id="bulkDeleteButton" class="btn btn-danger me-2" data-bs-toggle="tooltip" title="Delete Selected">
            <i class="bi bi-trash"></i> Delete Selected
        </button>
        <button type="button" id="bulkMarkCompletedButton" class="btn btn-success" data-bs-toggle="tooltip" title="Mark as Completed">
            <i class="bi bi-check-circle"></i> Mark as Completed
        </button>
    </div>

    <!-- Complaints Table -->
    <div class="table-responsive">
        @if($complaints->isEmpty())
            <div class="alert alert-info text-center">No complaints found.</div>
        @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="select-all" data-bs-toggle="tooltip" title="Select All">
                    </th>
                    <!-- Table Headers -->
                    <th>#ID</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Assigned Date</th>
                    <th>Complaint By</th>
                    <th>Assigned By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($complaints as $complaint)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="selected_complaints[]" value="{{ $complaint->id }}" class="select-box" data-bs-toggle="tooltip" title="Select Complaint">
                    </td>
                    <td>{{ $complaint->id }}</td>
                    <td>
                        <select
                            class="status-select"
                            data-complaint-id="{{ $complaint->id }}"
                            data-url="{{ route('admin.complaints.inlineUpdate', $complaint->id) }}"
                            data-bs-toggle="tooltip"
                            title="Change Status"
                        >
                            <option value="pending" {{ $complaint->comp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ongoing" {{ $complaint->comp_status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </td>
                    <td>{{ $complaint->comp_location }}</td>
                    <td>
                        @if($complaint->assigned_date)
                            <span class="assigned-date" data-bs-toggle="tooltip" title="Date when the complaint was assigned">
                                {{ $complaint->assigned_date->format('d M Y H:i') }}
                            </span>
                        @else
                            <span class="assigned-date">N/A</span>
                        @endif
                    </td>
                    <td>{{ $complaint->officer->name ?? 'N/A' }}</td>
                    <td>{{ $complaint->assignedBy->name ?? 'N/A' }}</td>
                    <td>
                        <!-- Action Buttons -->
                        <button type="button" class="btn btn-pastel-view btn-sm me-1" data-bs-toggle="modal" data-bs-target="#viewModal{{ $complaint->id }}" data-bs-toggle="tooltip" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>

                        <button type="button" class="btn btn-pastel-edit btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $complaint->id }}" data-bs-toggle="tooltip" title="Edit Complaint">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-pastel-delete btn-sm delete-button"
                            data-complaint-id="{{ $complaint->id }}"
                            data-bs-toggle="tooltip"
                            title="Delete Complaint"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $complaint->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel{{ $complaint->id }}">Complaint Details - ID: {{ $complaint->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="complaint-details">
                                    <h6>ID:</h6>
                                    <p class="detail-value">{{ $complaint->id }}</p>

                                    <hr>

                                    <h6>Status:</h6>
                                    <p class="detail-value text-capitalize">{{ $complaint->comp_status }}</p>

                                    <hr>

                                    <h6>Location:</h6>
                                    <p class="detail-value">{{ $complaint->comp_location }}</p>

                                    <hr>

                                    <h6>Assigned Date:</h6>
                                    <p class="detail-value">
                                        {{ $complaint->assigned_date ? $complaint->assigned_date->format('d M Y H:i') : 'N/A' }}
                                    </p>

                                    <hr>

                                    <h6>Complaint By:</h6>
                                    <p class="detail-value">{{ $complaint->officer->name ?? 'N/A' }}</p>

                                    <hr>

                                    <h6>Assigned By:</h6>
                                    <p class="detail-value">{{ $complaint->assignedBy->name ?? 'N/A' }}</p>

                                    <hr>

                                    <h6>Description:</h6>
                                    <p class="detail-value">{{ $complaint->comp_desc }}</p>

                                    <hr>

                                    @if($complaint->comp_image)
                                        <h6>Image:</h6>
                                        <img src="{{ asset('storage/' . $complaint->comp_image) }}" alt="Complaint Image" class="img-fluid rounded shadow-sm">
                                    @endif

                                    <!-- Add other complaint details as needed -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $complaint->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.complaints.update', $complaint->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $complaint->id }}">Edit Complaint - ID: {{ $complaint->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form Fields -->
                                    <div class="mb-3">
                                        <label for="comp_status{{ $complaint->id }}" class="form-label">Status</label>
                                        <select name="comp_status" id="comp_status{{ $complaint->id }}" class="form-select" required>
                                            <option value="pending" {{ $complaint->comp_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="ongoing" {{ $complaint->comp_status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                            <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_date{{ $complaint->id }}" class="form-label">Complaint Date</label>
                                        <input type="date" name="comp_date" id="comp_date{{ $complaint->id }}" class="form-control" value="{{ $complaint->comp_date }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_time{{ $complaint->id }}" class="form-label">Complaint Time</label>
                                        <input type="time" name="comp_time" id="comp_time{{ $complaint->id }}" class="form-control" value="{{ $complaint->comp_time }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_location{{ $complaint->id }}" class="form-label">Location</label>
                                        <input type="text" name="comp_location" id="comp_location{{ $complaint->id }}" class="form-control" value="{{ $complaint->comp_location }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_desc{{ $complaint->id }}" class="form-label">Description</label>
                                        <textarea name="comp_desc" id="comp_desc{{ $complaint->id }}" class="form-control" rows="3" required>{{ $complaint->comp_desc }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="no_of_cleaners{{ $complaint->id }}" class="form-label">Number of Cleaners</label>
                                        <input type="number" name="no_of_cleaners" id="no_of_cleaners{{ $complaint->id }}" class="form-control" value="{{ $complaint->no_of_cleaners }}" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_image{{ $complaint->id }}" class="form-label">Complaint Image</label>
                                        <input class="form-control" type="file" id="comp_image{{ $complaint->id }}" name="comp_image" accept="image/*">
                                        @if($complaint->comp_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $complaint->comp_image) }}" alt="Complaint Image" class="img-thumbnail rounded shadow-sm" width="150">
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Add other fields as needed -->
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
                <div class="modal fade" id="deleteModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $complaint->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $complaint->id }}">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this complaint?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST">
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

        <!-- Pagination and Page Size Selector -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Page Size Selector -->
            <div class="page-size-selector">
                <form method="GET" action="{{ route('admin.complaints') }}" class="d-flex align-items-center">
                    <label for="per_page" class="me-2">Show</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>

            <!-- Pagination Links -->
            <div>
                {{ $complaints->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Individual Delete Confirmation Modal (for selected complaint) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this complaint? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <input type="hidden" id="deleteComplaintId" value="">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmDeleteComplaintButton" class="btn btn-danger">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Bulk Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete the selected complaints? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmBulkDeleteButton" class="btn btn-danger">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Mark as Completed Confirmation Modal -->
    <div class="modal fade" id="bulkMarkCompletedModal" tabindex="-1" aria-labelledby="bulkMarkCompletedModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Mark as Completed</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to mark the selected complaints as completed?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmBulkMarkCompletedButton" class="btn btn-success">Yes, Mark as Completed</button>
          </div>
        </div>
      </div>
    </div>

</div>
@endsection
