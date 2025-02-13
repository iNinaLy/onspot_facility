@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <!-- External CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    <!-- Custom Inline Styles for a Clean & Sleek Look -->
    <style>
       
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #333;
            margin-right: 15px;
        }
        .metric-info {
            flex: 1;
        }
        .metric-title {
            font-size: 14px;
            font-weight: 500;
            color: #666;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        /* Remove border from the card container for the Donut Chart */
        .card {
            border: none;
        }

        /* Donut Chart Styles */
        .chart-container {
            position: relative;
        }
        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 28px;
            font-weight: 600;
            color: #333;
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
    <div class="row g-4">
        <!-- Left Column: Metrics Cards -->
        <div class="col-lg-4 d-flex flex-column gap-3">
            @php
                $metrics = [
                    [
                        'title' => 'Total Complaints Received',
                        'value' => $totalComplaints,
                        'icon' => 'bi bi-envelope-exclamation',
                    ],
                    [
                        'title' => 'Available Cleaners',
                        'value' => $activeCleaners,
                        'icon' => 'bi-person-check',
                    ],
                    [
                        'title' => 'Total Officers',
                        'value' => $totalOfficers,
                        'icon' => 'bi bi-person',
                    ],
                    [
                        'title' => 'Total Supervisors',
                        'value' => $totalSupervisors,
                        'icon' => 'bi bi-person',
                    ],
                ];
            @endphp

            @foreach($metrics as $metric)
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="{{ $metric['icon'] }}"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-title">{{ $metric['title'] }}</div>
                        <div class="metric-value">{{ $metric['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Column: Donut Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="font-weight-bold mb-3">Complaint Status</h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="complaintStatusChart"></canvas>
                        <!-- Center Text for Donut Chart -->
                        <div class="chart-center-text" id="chartCenterText">{{ $totalComplaints }}</div>
                    </div>
                    <div class="chart-legend mt-3">
                        @foreach ($complaintsByStatus as $status => $count)
                            @php
                                $color = match(strtolower($status)) {
                                    'completed' => '#b8e3e9',
                                    'ongoing' => '#93b1b5',
                                    'pending' => '#4f7c82',
                                    default => '#8e8e93',
                                };
                            @endphp
                            <div class="legend-item d-inline-flex align-items-center me-3">
                                <span style="display:inline-block;width:12px;height:12px;background-color:{{ $color }};border-radius:50%;margin-right:6px;"></span>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints Section -->
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
                                    $badgeClass = match ($status) {
                                        'completed' => 'status-completed',
                                        'ongoing' => 'status-ongoing',
                                        'pending' => 'status-pending',
                                        default => '',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($complaint->comp_status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('scripts')

@vite([
    'resources/admin/app.js',
    'resources/admin/dashboard.js',
    'resources/admin/complaint.js',
])
     
<script>
    const complaintStatusLabels = @json(array_keys($complaintsByStatus->toArray()));
    const complaintStatusData = @json(array_values($complaintsByStatus->toArray()));
    const colorPalette = ['#b8e3e9', '#93b1b5', '#4f7c82'];

    // Calculate total for center text
    const total = complaintStatusData.reduce((acc, val) => acc + val, 0);

    new Chart(document.getElementById('complaintStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: complaintStatusLabels.map(label => label.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: complaintStatusData,
                backgroundColor: colorPalette.slice(0, complaintStatusLabels.length),
                borderWidth: 0,
                hoverOffset: 6,
                borderRadius: 10,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { 
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#333',
                    bodyColor: '#333',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    cornerRadius: 6,
                    padding: 10,
                    displayColors: false,
                },
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    // Update center text with the total complaints count
    document.getElementById('chartCenterText').innerText = total;
</script>
@endpush
@endsection
