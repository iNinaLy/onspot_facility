@extends('layouts.admin')

@section('title', 'Manage Complaints')

@push('styles')
<link href="resources/admin/app.css" rel="stylesheet" />
<link href="resources/admin/complaint.css" rel="stylesheet" />

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
                            <!-- Modal Header -->
                            <div class="modal-header bg-gradient from-blue-500 to-teal-500 text-white">
                                <h5 class="modal-title" id="viewModalLabel{{ $complaint->id }}">Complaint Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="modal-body bg-gray-50">
                                <div class="complaint-info">
                                    <!-- Status Badge -->
                                     
                                    <div class="status-container mb-4 text-center">
                                        <span class="status-badge 
                                            @if($complaint->comp_status === 'completed') status-completed 
                                            @elseif($complaint->comp_status === 'ongoing') status-ongoing 
                                            @elseif($complaint->comp_status === 'pending') status-pending 
                                            @else status-unknown 
                                            @endif
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $complaint->comp_status)) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Complaint Details -->
                                    <div class="details-grid">
                                        <div class="detail-label">ID:</div>
                                        <div class="detail-value">{{ $complaint->id }}</div>

                                        <div class="detail-label">Location:</div>
                                        <div class="detail-value">{{ $complaint->comp_location }}</div>

                                        <div class="detail-label">Assigned Date:</div>
                                        <div class="detail-value">
                                            {{ $complaint->assigned_date ? $complaint->assigned_date->format('d M Y H:i') : 'N/A' }}
                                        </div>

                                        <div class="detail-label">Complaint By:</div>
                                        <div class="detail-value">{{ $complaint->officer->name ?? 'N/A' }}</div>

                                        <div class="detail-label">Assigned By:</div>
                                        <div class="detail-value">{{ $complaint->assignedBy->name ?? 'N/A' }}</div>

                                        <div class="detail-label">Description:</div>
                                        <div class="detail-value">{{ $complaint->comp_desc }}</div>

                                        @if($complaint->comp_image)
                                            <div class="detail-label">Image:</div>
                                            <div class="detail-value">
                                                <img src="{{ asset('storage/' . $complaint->comp_image) }}" alt="Complaint Image" class="complaint-image">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div class="modal-footer bg-gray-100">
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