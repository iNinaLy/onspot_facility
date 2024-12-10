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