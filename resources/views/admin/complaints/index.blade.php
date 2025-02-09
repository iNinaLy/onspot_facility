@extends('layouts.admin')

@section('title', 'Manage Complaints')

@push('styles')
<link href="resources/admin/app.css" rel="stylesheet" />
<link href="resources/admin/complaint.css" rel="stylesheet" />
@endpush


@section('content')
<div class="container my-5">

    <!-- Page Header -->
    <div class="heading">
        <h1 class="header-title">Manage Complaints</h1>
    </div>

    <!-- Metrics (Totals) -->
    <div class="row mb-5">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Total Complaints</h5>
                <p class="card-text">{{ $totalComplaints }}</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Pending</h5>
                <p class="card-text">{{ $pendingComplaints }}</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Ongoing</h5>
                <p class="card-text">{{ $ongoingComplaints }}</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="metrics-card text-center p-4">
                <h5 class="card-title">Completed</h5>
                <p class="card-text">{{ $completedComplaints }}</p>
            </div>
        </div>
    </div>

    <!-- Search + Status Filter Form -->
    <form method="GET" action="{{ route('admin.complaints') }}" class="mb-4">
        <div class="row g-3" style="display: flex; flex-wrap: wrap; justify-content: flex-end;">
           
            <div class="col-md-4"  >
                <select name="status" class="form-select" style=" border-radius: 20px;">
                    <option value="">All Statuses</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="ongoing"   {{ request('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit" style="border-radius: 17px;">Filter</button>
            </div>
        </div>
    </form>

    <!-- Bulk Actions Toolbar -->
    <div class="bulk-actions-toolbar mb-3">
        <button type="button" id="bulkDeleteButton" class="btn btn-danger me-2"  style="background-color: #d96464;border-radius:16px" data-bs-toggle="tooltip" title="Delete Selected">
            <i class="bi bi-trash"></i> Delete Selected
        </button>
        <button type="button" id="bulkMarkCompletedButton" class="btn btn-success" style="background-color:rgb(89, 134, 165);border-radius:16px; " data-bs-toggle="tooltip" title="Mark as Completed" >
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
                    <th>ID</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Assigned Date</th>
                    <th>Complaint By</th>
                    <th>Assigned By</th>
                    <th></th> <!-- Action column -->
                </tr>
            </thead>
            <tbody>
            @foreach($complaints as $complaint)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" name="selected_complaints[]" value="{{ $complaint->id }}" 
                               class="select-box" data-bs-toggle="tooltip" title="Select Complaint">
                    </td>
                    <td>{{ $complaint->id }}</td>
                    <td>
                        <!-- Inline status update via AJAX -->
                        <select
                            class="status-select"
                            data-complaint-id="{{ $complaint->id }}"
                            data-url="{{ route('admin.complaints.inlineUpdate', $complaint->id) }}"
                            data-bs-toggle="tooltip"
                            title="Change Status"
                        >
                            <option value="pending"   {{ $complaint->comp_status == 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="ongoing"   {{ $complaint->comp_status == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </td>
                    <td>{{ $complaint->comp_location }}</td>
                    <td>
                        @if($complaint->assigned_date)
                            <span class="assigned-date" data-bs-toggle="tooltip" title="Date assigned">
                                {{ $complaint->assigned_date->format('d M Y H:i') }}
                            </span>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $complaint->officer->name ?? 'N/A' }}</td>
                    <td>{{ $complaint->supervisor->name ?? 'N/A' }}</td>
                    <td>
                        <!-- View -->
                        <button type="button" class="btn btn-pastel-view btn-sm me-1"
                                data-bs-toggle="modal" data-bs-target="#viewModal{{ $complaint->id }}"
                                style="border-radius:12px"
                                data-bs-toggle="tooltip" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>

                        <!-- Edit -->
                        <button type="button" class="btn btn-pastel-edit btn-sm me-1" 
                                data-bs-toggle="modal" data-bs-target="#editModal{{ $complaint->id }}"
                                style="border-radius:12px"
                                data-bs-toggle="tooltip" title="Edit Complaint">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- Delete (individual) -->
                        <button type="button" class="btn btn-pastel-delete btn-sm delete-button"
                                data-complaint-id="{{ $complaint->id }}"
                                style="border-radius:12px"
                                data-bs-toggle="tooltip" title="Delete Complaint">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal{{ $complaint->id }}" 
                     tabindex="-1" aria-labelledby="viewModalLabel{{ $complaint->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-gradient from-blue-500 to-teal-500 text-white">
                                <h5 class="modal-title" id="viewModalLabel{{ $complaint->id }}">Complaint Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body bg-gray-50">
                                <div class="complaint-info">
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
                                                <img src="{{ asset('storage/' . $complaint->comp_image) }}"
                                                     alt="Complaint Image" class="complaint-image">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-gray-100">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /View Modal -->

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $complaint->id }}" 
                     tabindex="-1" aria-labelledby="editModalLabel{{ $complaint->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.complaints.update', $complaint->id) }}" 
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $complaint->id }}">
                                        Edit Complaint - ID: {{ $complaint->id }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="comp_status{{ $complaint->id }}" class="form-label">Status</label>
                                        <select name="comp_status" id="comp_status{{ $complaint->id }}" 
                                                class="form-select" required>
                                            <option value="pending"   {{ $complaint->comp_status == 'pending'   ? 'selected' : '' }}>Pending</option>
                                            <option value="ongoing"   {{ $complaint->comp_status == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                                            <option value="completed" {{ $complaint->comp_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_date{{ $complaint->id }}" class="form-label">Complaint Date</label>
                                        <input type="date" name="comp_date" id="comp_date{{ $complaint->id }}"
                                               class="form-control" value="{{ $complaint->comp_date }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_time{{ $complaint->id }}" class="form-label">Complaint Time</label>
                                        <input type="time" name="comp_time" id="comp_time{{ $complaint->id }}"
                                               class="form-control" value="{{ $complaint->comp_time }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_location{{ $complaint->id }}" class="form-label">Location</label>
                                        <input type="text" name="comp_location" id="comp_location{{ $complaint->id }}"
                                               class="form-control" value="{{ $complaint->comp_location }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_desc{{ $complaint->id }}" class="form-label">Description</label>
                                        <textarea name="comp_desc" id="comp_desc{{ $complaint->id }}"
                                                  class="form-control" rows="3" required>{{ $complaint->comp_desc }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="no_of_cleaners{{ $complaint->id }}" class="form-label">Number of Cleaners</label>
                                        <input type="number" name="no_of_cleaners" id="no_of_cleaners{{ $complaint->id }}"
                                               class="form-control" value="{{ $complaint->no_of_cleaners }}" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comp_image{{ $complaint->id }}" class="form-label">Complaint Image</label>
                                        <input class="form-control" type="file" id="comp_image{{ $complaint->id }}" 
                                               name="comp_image" accept="image/*">
                                        @if($complaint->comp_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $complaint->comp_image) }}" 
                                                     alt="Complaint Image" class="img-thumbnail rounded shadow-sm" width="150">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            @endforeach
            </tbody>
        </table>

        </div>
        @endif
    </div>

    <!-- Custom Pagination -->
    @if ($complaints->lastPage() > 1)
            <ul class="pagination mt-3">
                @if ($complaints->onFirstPage())
                    <li class="disabled"><span>&laquo;</span></li>
                @else
                    <li><a href="{{ $complaints->previousPageUrl() }}">&laquo;</a></li>
                @endif

                @for ($page = 1; $page <= $complaints->lastPage(); $page++)
                    @if ($page == $complaints->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $complaints->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endfor

                @if ($complaints->hasMorePages())
                    <li><a href="{{ $complaints->nextPageUrl() }}">&raquo;</a></li>
                @else
                    <li class="disabled"><span>&raquo;</span></li>
                @endif
            </ul>
        @endif

    </div>

    <!-- Individual Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
            <h5 class="modal-title">Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete the selected complaints? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary"  style="background-color:rgb(228, 227, 227); border-radius:12px; " data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmBulkDeleteButton" style="background-color:rgb(220, 117, 117); border-radius:12px; margin-left:2px; padding: 0.8 0.9;" class="btn btn-danger">Yes, Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Mark as Completed Confirmation Modal -->
    <div class="modal fade" id="bulkMarkCompletedModal" tabindex="-1" aria-labelledby="bulkMarkCompletedModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to mark the selected complaints as completed?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" style="background-color:rgb(228, 227, 227); border-radius:12px;" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmBulkMarkCompletedButton" style="background-color:rgb(97, 126, 149); border-radius:12px; margin-left:2px;  padding: 0.8 0.9;" class="btn btn-success" >Yes</button>
          </div>
        </div>
      </div>
    </div>

</div>
@endsection


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
<script>
    $(document).ready(function() {
    // CSRF token from meta
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 1) Individual delete button
    $('.delete-button').on('click', function() {
        var complaintId = $(this).data('complaint-id');
        $('#deleteComplaintId').val(complaintId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteComplaintButton').on('click', function() {
        var complaintId = $('#deleteComplaintId').val();
        var actionUrl = '{{ route("admin.complaints.bulkAction") }}';

        var form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });
        form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
        form.append('<input type="hidden" name="action" value="delete">');
        form.append('<input type="hidden" name="selected_complaints[]" value="' + complaintId + '">');
        $('body').append(form);
        form.submit();
    });

    // 2) Bulk delete
    $('#bulkDeleteButton').on('click', function(e) {
        e.preventDefault();
        var selectedComplaints = $('.select-box:checked').map(function() {
            return $(this).val();
        }).get();

        if (!selectedComplaints.length) {
            toastr.warning('Please select at least one complaint to delete.');
            return;
        }
        $('#bulkDeleteModal').modal('show');
    });

    $('#confirmBulkDeleteButton').on('click', function() {
        var actionUrl = '{{ route("admin.complaints.bulkAction") }}';
        var selectedComplaints = $('.select-box:checked').map(function() {
            return $(this).val();
        }).get();

        var form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });
        form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
        form.append('<input type="hidden" name="action" value="delete">');

        selectedComplaints.forEach(function(id) {
            form.append('<input type="hidden" name="selected_complaints[]" value="' + id + '">');
        });

        $('body').append(form);
        form.submit();
    });

    // 3) Bulk mark as completed
    $('#bulkMarkCompletedButton').on('click', function(e) {
        e.preventDefault();
        var selectedComplaints = $('.select-box:checked').map(function() {
            return $(this).val();
        }).get();

        if (!selectedComplaints.length) {
            toastr.warning('Please select at least one complaint to mark as completed.');
            return;
        }
        $('#bulkMarkCompletedModal').modal('show');
    });

    $('#confirmBulkMarkCompletedButton').on('click', function() {
        var actionUrl = '{{ route("admin.complaints.bulkAction") }}';
        var selectedComplaints = $('.select-box:checked').map(function() {
            return $(this).val();
        }).get();

        var form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });
        form.append('<input type="hidden" name="_token" value="' + csrfToken + '">');
        form.append('<input type="hidden" name="action" value="mark_completed">');

        selectedComplaints.forEach(function(id) {
            form.append('<input type="hidden" name="selected_complaints[]" value="' + id + '">');
        });

        $('body').append(form);
        form.submit();
    });

    // 4) "Select All" checkbox
    $('#select-all').on('click', function(){
        $('.select-box').prop('checked', this.checked);
        toggleBulkActions();
    });
    $('.select-box').on('change', toggleBulkActions);

    function toggleBulkActions() {
        var selectedCount = $('.select-box:checked').length;
        if (selectedCount > 0) {
            $('.bulk-actions-toolbar').addClass('active');
        } else {
            $('.bulk-actions-toolbar').removeClass('active');
        }
    }

    // 5) Inline status update
    $('.status-select').on('change', function(e) {
        e.stopPropagation();
        var selectElement = $(this);
        var complaintId   = selectElement.data('complaint-id');
        var newStatus     = selectElement.val();
        var url           = selectElement.data('url');

        selectElement.addClass('loading').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                comp_status: newStatus,
                _token: csrfToken
            },
            success: function(response) {
                selectElement.removeClass('loading').prop('disabled', false);
                if(response.status !== 'success') {
                    toastr.error(response.message || 'An error occurred while updating the status.');
                } else {
                    toastr.success('Status updated successfully.');
                }
            },
            error: function(xhr, status, error) {
                selectElement.removeClass('loading').prop('disabled', false);
                toastr.error('An error occurred while updating the status.');
            }
        });
    });

    // 6) Toastr notifications for success/error
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
});
</script>
@endpush
