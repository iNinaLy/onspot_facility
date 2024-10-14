@extends('layouts.admin')

@section('content')
<!-- Include Google Fonts in the layout -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .card {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .key-metric {
        font-size: 0.9rem;
        font-weight: 700;
        color: #6c757d;
    }
    .metric-value {
        font-size: 1.6rem;
        font-weight: bold;
        color: #333;
    }
    /* Dark mode styling */
    .dark-mode {
        background-color: #1d1f21;
        color: #f5f5f5;
    }
    .dark-mode .card {
        background-color: #333;
        color: #f5f5f5;
    }
    .dark-mode .btn-outline-secondary {
        color: #f5f5f5;
        border-color: #f5f5f5;
    }
    /* Style adjustments for the donut chart */
    .chart-container {
        position: relative;
        width: 100%;
        max-width: 250px;
        margin: 0 auto;
    }
    .chart-legend {
        display: flex;
        justify-content: center;
        margin-top: 15px;
        font-size: 0.85rem;
    }
    .chart-legend div {
        margin-right: 15px;
    }
    .profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .profile span {
        font-weight: bold;
        color: #333;
        margin-left: 10px;
    }
    .dark-mode .profile span {
        color: #f5f5f5;
    }
    .header {
        border-bottom: 1px solid #eaeaea;
        padding: 1rem;
        margin-bottom: 2rem;
        background-color: #fff;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
    }
    .header .profile {
        display: flex;
        align-items: center;
    }
    .header .profile button {
        margin-left: 1rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 20px;
    }
    /* Recent Complaints Table */
    .recent-complaints-table {
        width: 100%;
        border-collapse: collapse;
    }
    .recent-complaints-table th, .recent-complaints-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .recent-complaints-table th {
        font-weight: 700;
        color: #333;
    }
    .recent-complaints-table td {
        font-weight: 500;
        color: #666;
    }
    .dark-mode .recent-complaints-table th, .dark-mode .recent-complaints-table td {
        color: #f5f5f5;
        border-color: #555;
    }
    .header-image {
        width: 100%;
        height: 20rem;
        margin-bottom: 20px;
        border-radius: 25px;
      
    }
</style>

<div class="main-content-wrapper fade-in">
    <!-- Header Section -->
    <div class="header d-flex justify-content-between align-items-center mb-4 fixed-top" style="background-color: #fff; padding: 1rem; z-index: 1000; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <h1 class="h4 text-dark">Dashboard</h1>
        <div class="profile d-flex align-items-center">
            <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary ml-3">Dark Mode</button>
        </div>
    </div>

    <!-- Image Section -->
    <img src="/images/dashboard.png" alt="Dashboard Image" class="header-image">

    <div class="row">
        <!-- Left Column: Key Metrics -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <span class="key-metric">TOTAL COMPLAINTS</span>
                    <div class="metric-value">{{ $totalComplaints }}</div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <span class="key-metric">ACTIVE CLEANERS</span>
                    <div class="metric-value">{{ $activeCleaners }}</div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <span class="key-metric">TOTAL OFFICERS</span>
                    <div class="metric-value">{{ $totalOfficers }}</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Complaint Status Donut Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="font-weight-bold mb-4">Complaint Status</h5>
                    <div class="chart-container">
                        <canvas id="complaintStatusChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3">
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

    <!-- Recent Complaints Section with "See All" button -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="font-weight-bold text-dark mb-0">Recent Complaints</h5>
                        <a href="{{ route('admin.complaints') }}" class="text-primary" style="font-weight: 500; text-decoration: none;">See All</a>
                    </div>
                    <table class="recent-complaints-table">
                        <thead>
                            <tr>
                                <th>Complaint Description</th>
                                <th>Officer</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentComplaints as $complaint)
                                @php $complaint->load('officer') @endphp
                                <tr>
                                    <td>{{ $complaint->comp_desc }}</td>
                                    <td>Complaint by: {{ $complaint->officer->officer_name }}</td>
                                    <td>{{ $complaint->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
    const colorPalette = ['#4a90e2', '#f5a623', '#d0021b'];

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
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection
