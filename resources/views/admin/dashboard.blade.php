@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
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
            <!-- Metric Card Template -->
            @php
                $metrics = [
                    [
                        'title' => 'Total Complaints',
                        'value' => $totalComplaints,
                        'icon' => 'bi-exclamation-triangle',
                        'icon_class' => 'icon-black'
                    ],
                    [
                        'title' => 'Available Cleaners',
                        'value' => $activeCleaners,
                        'icon' => 'bi-person-check',
                        'icon_class' => 'icon-black'
                    ],
                    [
                        'title' => 'Total Officers',
                        'value' => $totalOfficers,
                        'icon' => 'bi-shield-fill-check',
                        'icon_class' => 'icon-black'
                    ],
                    [
                        'title' => 'Total Supervisors',
                        'value' => $totalSupervisors,
                        'icon' => 'bi-person-fill',
                        'icon_class' => 'icon-black'
                    ],
                ];
            @endphp

            @foreach($metrics as $metric)
                <div class="metric-card d-flex align-items-center">
                    <div class="metric-icon icon-background me-3">
                        <i class="{{ $metric['icon'] }} {{ $metric['icon_class'] }}"></i>
                    </div>
                    <div class="metric-info text-center">
                        <h6 class="metric-title">{{ $metric['title'] }}</h6>
                        <h3 class="metric-value">{{ $metric['value'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Column: Donut Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="font-weight-bold mb-3">Complaint Status</h5>
                    <div class="chart-container" style="height: 300px; position: relative;">
                        <canvas id="complaintStatusChart"></canvas>
                        <!-- Optional: Center Text -->
                        <div class="chart-center-text" id="chartCenterText">{{ $totalComplaints }}</div>
                    </div>
                    <div class="chart-legend mt-2">
                        @foreach ($complaintsByStatus as $status => $count)
                            @php
                                $color = match(strtolower($status)) {
                                    'completed' => '#b8e3e9',
                                    'ongoing' => '#93b1b5',
                                    'pending' => '#4f7c82',
                                    default => '#8e8e93',
                                };
                            @endphp
                            <div class="legend-item">
                                <span style="background-color: {{ $color }};"></span>
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
                                <span class="status-badge {{ $badgeClass }}">
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

    // Calculate total for center text (optional)
    const total = complaintStatusData.reduce((acc, val) => acc + val, 0);

    new Chart(document.getElementById('complaintStatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: complaintStatusLabels.map(label => label.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: complaintStatusData,
                backgroundColor: colorPalette.slice(0, complaintStatusLabels.length),
                borderWidth: 0,
                hoverOffset: 6, // Increased hover offset for better interactivity
                borderRadius: 10, // Rounded edges
                borderSkipped: false, // Show border on all edges
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Increased cutout for a thinner donut
            plugins: { 
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#333',
                    bodyColor: '#333',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    cornerRadius: 4,
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

    // Optional: Add center text (requires additional CSS)
    document.getElementById('chartCenterText').innerText = total;
</script>
@endpush
@endsection
