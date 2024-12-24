<!-- resources/views/admin/dashboard.blade.php -->

@extends('layouts.admin')

@section('title', 'Admin Dashboard')


@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
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
    @vite([
        'resources/admin/app.js',
        'resources/admin/dashboard.js',
        'resources/admin/complaint.js',
    ])
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
 