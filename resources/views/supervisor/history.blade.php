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
            background-color: #f0f4f8; /* Softer grey background for the card */
            border-radius: 10px; /* Rounded corners for a modern feel */
            border: none; /* Remove card border */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow for depth */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Smooth transitions */
        }

        .card:hover {
            transform: translateY(-3px); /* Slight lift effect on hover */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* More shadow on hover */
        }

        .btn-outline-primary {
            color: #2E5675; /* Darker blue for button */
            border-color: #2E5675; /* Matching border color */
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
            background-color: #ceb8f5; /* Yellow for in-progress */
        }

        .badge-secondary {
            background-color: #6c757d; /* Grey for pending */
        }

        h1 {
            color: #343a40;
            font-size: 1.5rem;
            margin-bottom: 3rem;
            margin-top: 4rem;
            padding-top: 20px;
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
                            <a href="#" class="btn btn-outline-primary btn-block">View Details</a>
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
</x-app-layout>
