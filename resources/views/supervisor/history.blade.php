<x-app-layout>
    <style>
        body {
            background-color: #f4f6f9; /* Light grey background for a clean look */
            font-family: 'Arial', sans-serif; /* Simple, modern font */
        }

        .text-center {
            text-align: left !important;
        }

        .card {
            background-color: #ffffff; /* White background for the card */
            border-radius: 10px; /* Rounded corners for a modern feel */
            border: none; /* Remove card border */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Smooth transitions */
        }

        .card:hover {
            transform: translateY(-4px); /* Slight lift effect on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* More shadow on hover */
        }

        .btn-outline-primary {
            color: #2E5675; /* Darker blue for button */
            border-color: #2E5675; /* Matching border color */
            transition: background-color 0.3s, color 0.3s; /* Transition for hover effect */
        }

        .btn-outline-primary:hover {
            background-color: #2E5675; /* Background color on hover */
            color: white; /* White text on hover */
        }

        .badge {
            display: inline-block;
            padding: .25em .4em;
            margin-bottom: 1rem;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .badge-success {
            background-color: #28a745; /* Green for completed */
        }

        .badge-warning {
            background-color: #ffc107; /* Yellow for in-progress */
        }

        .badge-secondary {
            background-color: #6c757d; /* Grey for pending */
        }

        h1 {
            color: #343a40;
            font-size: 2rem; /* Increased size for emphasis */
            margin-bottom: 2rem; /* Space below heading */
            margin-top: 4rem;
            text-align: center; /* Centered heading */
        }

        .container {
            max-width: 1200px; /* Limit container width for better readability */
            margin: 0 auto; /* Center align the container */
            padding: 20px; /* Padding inside the container */
        }

        .card-body {
            padding: 15px; /* Padding inside cards */
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px; /* Add spacing between cards */
        }

        .col-md-4 {
            flex: 1 0 calc(33.333% - 15px); /* Flex basis to ensure 3 cards per row */
            box-sizing: border-box;
        }

        /* Ensure responsive design on smaller screens */
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 1 0 calc(50% - 15px); /* 2 cards per row on tablet */
            }
        }

        @media (max-width: 576px) {
            .col-md-4 {
                flex: 1 0 100%; /* 1 card per row on mobile */
            }
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Modal styles */
        .modal-header {
            background-color: #e3f2fd; /* Light pastel blue for the modal header */
            color: #000; /* Dark text for contrast */
            border-bottom: none; /* No border for a cleaner look */
        }

        .modal-content {
            border-radius: 10px; /* Rounded corners for the modal */
            background-color: #f9f9f9; /* Soft pastel background for modal */
        }

        .modal-body {
            padding: 20px; /* Increased padding for better spacing */
        }

        .modal-body p {
            margin-bottom: 15px; /* Space below each paragraph in modal */
            color: #555; /* Softer text color */
        }

        .modal-body h6 {
            margin-top: 20px; /* Space above assigned cleaners heading */
            font-weight: bold; /* Bold for emphasis */
        }

        .modal-body ul {
            padding-left: 20px; /* Indent for cleaner list */
        }
    </style>

    <div class="container">
        <h1 class="text-center">Complaint History</h1> <!-- Added heading -->

        <div class="row">
            @forelse($complaints as $complaint)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm rounded">
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold">Task Assigned</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $complaint->updated_at->format('H:i A') }}</h6>
                            <p class="card-text"><strong>Floor:</strong> {{ $complaint->location ?? 'N/A' }}</p>
                            <p class="card-text">
                                <strong>Assigned Cleaners:</strong> 
                                @if ($complaint->cleaners->isNotEmpty())
                                    {{ $complaint->cleaners->count() }} Cleaners
                                @else
                                    Not Assigned
                                @endif
                            </p>
                            <p class="card-text">
                                <strong>Status:</strong> 
                                <span class="badge badge-{{ $complaint->comp_status == 'completed' ? 'success' : ($complaint->comp_status == 'in progress' ? 'warning' : 'secondary') }}">{{ ucfirst($complaint->comp_status) }}</span>
                            </p>
                            <a href="#" class="btn btn-outline-primary btn-block" 
                               data-toggle="modal" 
                               data-target="#complaintModal"
                               data-floor="{{ $complaint->location ?? 'N/A' }}"
                               data-date="{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}"
                               data-officer="{{ $complaint->officer->officer_name ?? 'Unknown Officer' }}"
                               data-status="{{ ucfirst($complaint->comp_status) }}"
                               data-description="{{ $complaint->comp_desc }}"
                               data-cleaners="{{ json_encode($complaint->cleaners) }}">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">No complaints found.</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Floor:</strong> <span id="modal-floor"></span></p>
                    <p><strong>Date:</strong> <span id="modal-date"></span></p>
                    <p><strong>Complaint by:</strong> <span id="modal-officer"></span></p>
                    <p><strong>Status:</strong> <span id="modal-status" class="badge"></span></p>
                    <p><strong>Description:</strong> <span id="modal-description"></span></p>
                    <h6>Assigned Cleaners:</h6>
                    <ul id="modal-cleaners-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle cleaner selection -->
    <script>
        $('#complaintModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var floor = button.data('floor'); // Extract info from data-* attributes
            var date = button.data('date');
            var officer = button.data('officer');
            var status = button.data('status');
            var description = button.data('description');
            var cleaners = button.data('cleaners'); // Extract cleaners data

            // Populate the modal fields
            var modal = $(this);
            modal.find('#modal-floor').text(floor);
            modal.find('#modal-date').text(date);
            modal.find('#modal-officer').text(officer);
            modal.find('#modal-status').text(status);

            // Set the badge class based on status
            var statusClass = status.toLowerCase() === 'completed' ? 'badge-success' : (status.toLowerCase() === 'in progress' ? 'badge-warning' : 'badge-secondary');
            modal.find('#modal-status').addClass(statusClass).removeClass('badge-success badge-warning badge-secondary');

            modal.find('#modal-description').text(description);

            // Populate the assigned cleaners list
            var cleanersList = modal.find('#modal-cleaners-list');
            cleanersList.empty(); // Clear existing entries
            if (cleaners.length) {
                cleaners.forEach(function(cleaner) {
                    cleanersList.append('<li>' + cleaner.cleaner_name + ' (' + cleaner.cleaner_phoneNo + ')</li>');
                });
            } else {
                cleanersList.append('<li>No cleaners assigned.</li>');
            }
        });
    </script>
</x-app-layout>
