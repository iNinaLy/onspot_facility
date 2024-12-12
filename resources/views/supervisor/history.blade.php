{{-- resources/views/history.blade.php --}}

@extends('layouts.app')

@section('title', 'Complaint History')

@push('styles')
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
@endpush

@section('content')
    <div class="container">
        <h1 class="heading">Complaint History</h1>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search complaints...">
            <input type="date" id="dateFilter">
        </div>

        <!-- Completed Complaints -->
        <div class="section">
            <h2 class="section-heading">Completed Complaints</h2>

            <!-- Button Group -->
            <div class="btn-group">
                <button class="active" id="showTodayBtn">Today</button>
                <button class="inactive" id="showThisWeekBtn">This Week</button>
                <button class="inactive" id="showOlderBtn">Older</button>
            </div>

            <!-- Completed Today -->
            <div id="completedToday">
                <h3 class="sub-heading">Today</h3>
                @forelse($todaysComplaints as $complaint)
                    <div class="card" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                        <div class="card-header">
                            <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                            <span class="status badge-completed">
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                            </span>
                        </div>
                        <div>
                            <p class="description">{{ $complaint->comp_desc }}</p>
                            <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                            <div id="details-{{ $complaint->id }}" class="toggle-content">
                                <p><strong>Complaint By:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                                <p class="complaint-date">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i> <strong>Complaint Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                </p>
                                <p><strong><i class="fas fa-calendar-alt" aria-hidden="true"></i> Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</p>
                                
                                <h6>Assigned Cleaners:</h6>
                                <ul>
                                    @forelse($complaint->cleaners as $cleaner)
                                        <li>
                                            {{ $cleaner->cleaner_name }} - 
                                            @if($cleaner->cleaner_phoneNo)
                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                    <i class="fas fa-phone" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    @empty
                                        <li>No cleaners assigned.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state empty-state-completed">
                        <i class="fas fa-info-circle" aria-hidden="true"></i> 
                        There are currently no completed tasks. Our cleaners are diligently handling their assignments. Please check back soon.
                    </p>
                @endforelse
            </div>

            <!-- Completed This Week -->
            <div id="completedThisWeek" style="display: none;">
                <h3 class="sub-heading">This Week</h3>
                @forelse($thisWeeksComplaints as $complaint)
                    <div class="card" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                        <div class="card-header">
                            <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                            <span class="status badge-completed">
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                            </span>
                        </div>
                        <div>
                            <p class="description">{{ $complaint->comp_desc }}</p>
                            <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                            <div id="details-{{ $complaint->id }}" class="toggle-content">
                                <p><strong>Complaint By:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                                <p class="complaint-date">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i> <strong>Complaint Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                </p>
                                <p><strong><i class="fas fa-calendar-alt" aria-hidden="true"></i> Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</p>
                                
                                <h6>Assigned Cleaners:</h6>
                                <ul>
                                    @forelse($complaint->cleaners as $cleaner)
                                        <li>
                                            {{ $cleaner->cleaner_name }} - 
                                            @if($cleaner->cleaner_phoneNo)
                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                    <i class="fas fa-phone" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    @empty
                                        <li>No cleaners assigned.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state empty-state-completed">
                        <i class="fas fa-info-circle" aria-hidden="true"></i> 
                        There are currently no completed tasks. Our cleaners are diligently handling their assignments. Please check back soon.
                    </p>
                @endforelse
            </div>

            <!-- Completed Older -->
            <div id="completedOlder" style="display: none;">
                <h3 class="sub-heading">Older</h3>
                @forelse($olderComplaints as $complaint)
                    <div class="card" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                        <div class="card-header">
                            <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                            <span class="status badge-completed">
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                            </span>
                        </div>
                        <div>
                            <p class="description">{{ $complaint->comp_desc }}</p>
                            <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                            <div id="details-{{ $complaint->id }}" class="toggle-content">
                                <p><strong>Complaint By:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                                <p class="complaint-date">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i> <strong>Complaint Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                </p>
                                <p><strong><i class="fas fa-calendar-alt" aria-hidden="true"></i> Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</p>
                                
                                <h6>Assigned Cleaners:</h6>
                                <ul>
                                    @forelse($complaint->cleaners as $cleaner)
                                        <li>
                                            {{ $cleaner->cleaner_name }} - 
                                            @if($cleaner->cleaner_phoneNo)
                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                    <i class="fas fa-phone" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    @empty
                                        <li>No cleaners assigned.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state empty-state-completed">
                        <i class="fas fa-info-circle" aria-hidden="true"></i> 
                        There are currently no completed tasks yet. Our cleaners are diligently handling their assignments. Check back soon!
                    </p>
                @endforelse

                <!-- Pagination for Older Complaints -->
                @if($olderComplaints->hasPages())
                    <div class="pagination">
                        {{ $olderComplaints->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Ongoing Complaints -->
        <div class="section">
            <h2 class="section-heading">Ongoing Complaints</h2>
            <div id="ongoingComplaints">
                @forelse($ongoingComplaints as $complaint)
                    <div class="card" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                        <div class="card-header">
                            <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                            <span class="status badge-ongoing">
                                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                            </span>
                        </div>
                        <div>
                            <p class="description">{{ $complaint->comp_desc }}</p>
                            <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                            <div id="details-{{ $complaint->id }}" class="toggle-content">
                                <p><strong>Complaint By:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                                <p class="complaint-date">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i> <strong>Complaint Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                </p>
                                <p><strong><i class="fas fa-calendar-alt" aria-hidden="true"></i> Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</p>
                                
                                <h6>Assigned Cleaners:</h6>
                                @if($complaint->cleaners->isEmpty())
                                    <p>No cleaners assigned.</p>
                                @else
                                    <table class="table table-sm table-borderless assigned-cleaners">
                                        <thead>
                                            <tr>
                                                <th>Cleaner Name</th>
                                                <th>Phone Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($complaint->cleaners as $cleaner)
                                                <tr>
                                                    <td>{{ $cleaner->cleaner_name }}</td>
                                                    <td>
                                                        @if($cleaner->cleaner_phoneNo)
                                                            <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                <i class="bi bi-telephone"></i> {{ $cleaner->cleaner_phoneNo }}
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-state empty-state-ongoing">There are no ongoing complaints at the moment.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
    'resources/supervisor/complaint.js',
    'resources/supervisor/history.js',
])
    </script>
@endpush
