@extends('layouts.admin')

@section('content')
<!-- Include Google Fonts in the layout -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(252deg, #f5f7fa, #dfe6eb); /* Softer background gradient */
        color: #333;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Smaller Card Style */
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        background-color: #fff;
        margin-bottom: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Slight hover effect */
    }

    .key-metric {
        font-size: 0.85rem; /* Smaller font */
        font-weight: 600;
        color: #1f3d5a;
    }

    .metric-value {
        font-size: 1.5rem; /* Smaller font */
        font-weight: bold;
        color: #2e5675;
    }

    /* Dark mode styling */
    .dark-mode {
        background-color: #1d1f21;
        color: #f5f5f5;
    }

    .dark-mode .card {
        background-color: #2e2e3e;
        color: #f5f5f5;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .dark-mode .key-metric, .dark-mode .metric-value {
        color: #ffffff;
    }

    /* Button styling */
    #darkModeToggle {
        background-color: #2e5675;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        transition: background-color 0.3s ease;
    }

    #darkModeToggle:hover {
        background-color: #1f3d5a;
    }

    /* Smaller Chart Container */
    .chart-container {
        position: relative;
        width: 100%;
        max-width: 250px; /* Reduced size for donut chart */
        margin: 0 auto;
        padding: 0; /* Removed extra padding */
    }

    /* Table Styling */
    .recent-complaints-table {
        width: 100%;
        border-collapse: collapse;
        background-color: rgba(255, 255, 255, 0.9);
    }

    .recent-complaints-table th {
        background-color: #2e5675;
        color: #fff;
        padding: 0.5rem; /* Reduced padding */
        font-weight: 700;
        text-align: left;
        border-bottom: 2px solid #e0e0e0;
    }

    .recent-complaints-table td {
        padding: 0.5rem; /* Reduced padding */
        border-bottom: 1px solid #e0e0e0;
        color: #333;
    }

    .recent-complaints-table tr:hover {
        background-color: #f5f5f5;
    }

    /* See All Button Styling */
    .see-all-btn {
        background-color: #2e5675;
        color: #fff;
        padding: 0.3rem 0.8rem; /* Smaller button */
        border-radius: 20px;
        border: none;
        font-size: 0.85rem;
        transition: background-color 0.3s ease;
    }

    .see-all-btn:hover {
        background-color: #1f3d5a;
    }

    /* Additional styles for header */
    .header {
        background-color: #fff;
        border-bottom: 1px solid #eee;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .header .btn-sm {
        background-color: #2e5675;
        color: white;
    }

    

    /* Chart Legend */
    .chart-legend div {
        display: inline-block;
        margin-right: 10px;
        font-size: 0.8rem; /* Smaller font */
        color: #333;
    }

    .dark-mode .chart-legend div {
        color: #f5f5f5;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }
    }
</style>

<div class="main-content-wrapper fade-in">
    <!-- Header Section -->
    <div class="header d-flex justify-content-between align-items-center mb-4 fixed-top">
        <h1 class="h4 text-dark">Dashboard</h1>
        <div class="profile d-flex align-items-center">
            <button id="darkModeToggle" class="btn btn-sm">Dark Mode</button>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Left Column: Key Metrics -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <span class="key-metric">TOTAL COMPLAINTS</span>
                    <div class="metric-value">{{ $totalComplaints }}</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <span class="key-metric">AVAILABLE CLEANERS</span>
                    <div class="metric-value">{{ $activeCleaners }}</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <span class="key-metric">TOTAL OFFICERS</span>
                    <div class="metric-value">{{ $totalOfficers }}</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <span class="key-metric">TOTAL SUPERVISORS</span>
                    <div class="metric-value">{{ $totalSupervisors }}</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Complaint Status Donut Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="font-weight-bold mb-3">Complaint Status</h5>
                    <div class="chart-container">
                        <canvas id="complaintStatusChart"></canvas>
                    </div>
                    <div class="chart-legend mt-2">
                        @foreach ($complaintsByStatus as $status => $count)
                            <div>
                                <span style="color: {{ $loop->index == 0 ? '#4a90e2' : ($loop->index == 1 ? '#f5a623' : '#d0021b') }};">■</span> {{ ucfirst($status) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints Section -->
    <div class="recent-complaints-section mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="font-weight-bold text-dark">Recent Complaints</h5>
            <!-- See All Button -->
            <a href="{{ route('admin.complaints') }}" class="see-all-btn">See All</a>
        </div>
        <table class="recent-complaints-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentComplaints as $complaint)
                    <tr>
                        <td>{{ $complaint->id }}</td>
                        <td>{{ $complaint->comp_date }}</td>
                        <td>{{ $complaint->comp_time }}</td>
                        <td>{{ $complaint->comp_desc }}</td>
                        <td>{{ $complaint->comp_location }}</td>
                        <td>{{ $complaint->comp_status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dark Mode Toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        document.querySelectorAll('.card').forEach(card => card.classList.toggle('dark-mode'));
    });

    // Prepare data for Complaint Status Chart
    const complaintStatusLabels = @json(array_keys($complaintsByStatus->toArray()));
    const complaintStatusData = @json(array_values($complaintsByStatus->toArray()));
    const colorPalette = ['#709a9e', '#ede491', '#a86060'];

    // Complaint Status Donut Chart
    const complaintStatusCtx = document.getElementById('complaintStatusChart').getContext('2d');
    new Chart(complaintStatusCtx, {
        type: 'doughnut',
        data: {
            labels: complaintStatusLabels,
            datasets: [{
                data: complaintStatusData,
                backgroundColor: colorPalette.slice(0, complaintStatusLabels.length),
                borderWidth: 3,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '75%', /* Increased cutout for a smaller center */
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection
