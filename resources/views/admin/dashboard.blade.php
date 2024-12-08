<!-- resources/views/admin/dashboard.blade.php -->

@extends('layouts.admin')

@section('title', 'Admin Dashboard')


@push('styles')

<!-- Include Bootstrap CSS (if not already included in your layout) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Include Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

@push('styles')
    <!-- Additional Styles if Needed -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>

    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(252deg, #f5f7fa, #dfe6eb);
        color: #333;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Sidebar styling */
    #sidebar-container {
        background-color: #ffffff;
        transition: background-color 0.3s ease;
    }

    /* Card Style */
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
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .key-metric {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1f3d5a;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2e5675;
    }

    /* Button styling */
    .see-all-btn {
        background-color: #2e5675;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        border: none;
        font-size: 0.85rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .see-all-btn:hover {
        background-color: #1f3d5a;
    }

    /* Header */
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

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }
    }
    
    .main-content-wrapper {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Recent Complaints Section */
    .recent-complaints-section {
        background-color: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .recent-complaints-section h5 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2e5675;
        margin-bottom: 1rem;
    }

    .recent-complaints-section .see-all-btn {
        background-color: #2e5675;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        border: none;
        font-size: 0.85rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .recent-complaints-section .see-all-btn:hover {
        background-color: #1f3d5a;
    }

    .recent-complaints-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #f9fafb;
        border-radius: 12px;
        overflow: hidden;
    }

    .recent-complaints-table thead {
        background-color: #2e5675;
    }

    .recent-complaints-table th {
        color: #fff;
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .recent-complaints-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .recent-complaints-table tbody tr:hover {
        background-color: #eef3f8;
    }

    .recent-complaints-table td {
        padding: 0.75rem 1rem;
        color: #333;
        font-size: 0.85rem;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Status Badge Styling */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
        text-transform: capitalize;
    }

    .status-completed {
        background-color: #b8e3e9;
    }

    .status-ongoing {
        background-color: #93b1b5;
    }

    .status-pending {
        background-color: #4f7c82;
    }

    /* Responsive Table */
    @media (max-width: 576px) {
        .recent-complaints-table th, .recent-complaints-table td {
            padding: 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Chart Legend Styling */
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .chart-legend div {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #8e8e93;
    }

    .chart-legend span {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 0.5rem;
        border-radius: 3px;
    }

    </style>
@endpush

@section('content')
    <div class="main-content-wrapper fade-in">
        <!-- Header Section -->
        <div class="header d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Dashboard</h1>
        </div>

        <!-- Dashboard Content -->
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
                                @php
                                    $color = '';
                                    switch(strtolower($status)) {
                                        case 'completed':
                                            $color = '#b8e3e9';
                                            break;
                                        case 'ongoing':
                                            $color = '#93b1b5';
                                            break;
                                        case 'pending':
                                            $color = '#4f7c82';
                                            break;
                                        default:
                                            $color = '#8e8e93';
                                    }
                                @endphp
                                <div>
                                    <span style="background-color: {{ $color }};"></span> {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Redesigned Recent Complaints Section -->
        <div class="recent-complaints-section mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-weight-bold text-dark">Recent Complaints</h5>
                <a href="{{ route('admin.complaints') }}" class="see-all-btn">See All</a>
            </div>
            <div class="table-responsive">
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
                                <td>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($complaint->comp_time)->format('h:i A') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($complaint->comp_desc, 50, '...') }}</td>
                                <td>{{ $complaint->comp_location }}</td>
                                <td>
                                    @php
                                        $status = strtolower($complaint->comp_status);
                                        $badgeClass = '';
                                        switch($status) {
                                            case 'completed':
                                                $badgeClass = 'status-completed';
                                                break;
                                            case 'ongoing':
                                                $badgeClass = 'status-ongoing';
                                                break;
                                            case 'pending':
                                                $badgeClass = 'status-pending';
                                                break;
                                            default:
                                                $badgeClass = '';
                                        }
                                    @endphp
                                    <span class="status-badge {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', ucfirst($complaint->comp_status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @push('scripts')
        <script>
            // Prepare data for Complaint Status Chart
            const complaintStatusLabels = @json(array_keys($complaintsByStatus->toArray()));
            const complaintStatusData = @json(array_values($complaintsByStatus->toArray()));
            const colorPalette = ['#b8e3e9', '#93b1b5', '#4f7c82']; // Updated color palette

            // Complaint Status Donut Chart
            const complaintStatusCtx = document.getElementById('complaintStatusChart').getContext('2d');
            new Chart(complaintStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: complaintStatusLabels.map(label => label.replace('_', ' ').toUpperCase()),
                    datasets: [{
                        data: complaintStatusData,
                        backgroundColor: colorPalette.slice(0, complaintStatusLabels.length),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            titleColor: '#1c1c1e',
                            bodyColor: '#1c1c1e',
                            borderColor: '#e5e5ea',
                            borderWidth: 1,
                            borderRadius: 10,
                            padding: 10,
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
 