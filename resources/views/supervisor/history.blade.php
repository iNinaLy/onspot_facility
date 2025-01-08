{{-- resources/views/supervisor/history.blade.php --}}

@extends('layouts.app')

@section('title', 'Complaint History')

@section('content')
    <div class="container">
        <h1 class="heading">History</h1>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search complaints...">
            <input type="date" id="dateFilter">
        </div>

        <!-- Main Tab Navigation (Complaint Status) -->
        <div class="tab-navigation">
            <button class="active" data-tab="ongoing">Ongoing Complaints</button>
            <button class="inactive" data-tab="completed">Completed Complaints</button>
        </div>

        <!-- Tab Content -->
        <div id="tab-content" class="tab-content">
            <!-- Ongoing Complaints Tab -->
            <div class="tab-pane" id="ongoing" style="display: block;">
                <!-- Sub-Tab Navigation (Timeframe) -->
                <div class="btn-group">
                    <button class="active" data-filter="today">Today</button>
                    <button class="inactive" data-filter="thisWeek">This Week</button>
                    <button class="inactive" data-filter="older">Older</button>
                </div>

                <!-- Time Categories for Ongoing Complaints -->
                <div class="time-category" id="ongoingToday">
                    <h3 class="sub-heading">Today</h3>
                    @forelse($ongoingToday as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are no ongoing complaints today.
                        </p>
                    @endforelse
                </div>

                <div class="time-category" id="ongoingThisWeek" style="display: none;">
                    <h3 class="sub-heading">This Week</h3>
                    @forelse($ongoingThisWeek as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                - 
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                - N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are no ongoing complaints this week.
                        </p>
                    @endforelse
                </div>

                <!-- Older Ongoing Complaints -->
                <div class="time-category" id="ongoingOlder" style="display: none;">
                    <h3 class="sub-heading">Older</h3>
                    @forelse($ongoingOlder as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                - 
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                - N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are no ongoing complaints older than this week.
                        </p>
                    @endforelse

                    <!-- Load More Button -->
                    @if($ongoingOlder->hasMorePages())
                        <div class="load-more-container ongoing-load-more">
                            <button class="btn-load-more" data-status="ongoing" data-filter="older" data-page="{{ $ongoingOlder->currentPage() + 1 }}">
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Completed Complaints Tab -->
            <div class="tab-pane" id="completed" style="display: none;">
                <!-- Sub-Tab Navigation (Timeframe) -->
                <div class="btn-group">
                    <button class="active" data-filter="today">Today</button>
                    <button class="inactive" data-filter="thisWeek">This Week</button>
                    <button class="inactive" data-filter="older">Older</button>
                </div>

                <!-- Time Categories for Completed Complaints -->
                <div class="time-category" id="completedToday">
                    <h3 class="sub-heading">Today</h3>
                    @forelse($completedToday as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are currently no completed complaints today.
                        </p>
                    @endforelse
                </div>

                <div class="time-category" id="completedThisWeek" style="display: none;">
                    <h3 class="sub-heading">This Week</h3>
                    @forelse($completedThisWeek as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are currently no completed complaints this week.
                        </p>
                    @endforelse
                </div>

                <!-- Older Completed Complaints -->
                <div class="time-category" id="completedOlder" style="display: none;">
                    <h3 class="sub-heading">Older</h3>
                    @forelse($completedOlder as $complaint)
                        <div class="card fade-in" data-date="{{ $complaint->assigned_date }}">
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button class="btn-details" data-id="{{ $complaint->id }}" aria-expanded="false" aria-controls="details-{{ $complaint->id }}">View Details</button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>{{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Cleaners:</strong>
                                            @if($complaint->cleaners->isEmpty())
                                                <span>No cleaners assigned.</span>
                                            @else
                                                <ul class="cleaner-list">
                                                    @foreach($complaint->cleaners as $cleaner)
                                                        <li>
                                                            {{ $cleaner->cleaner_name }}
                                                            @if($cleaner->cleaner_phoneNo)
                                                                - 
                                                                <a href="tel:{{ $cleaner->cleaner_phoneNo }}" class="phone-link" aria-label="Call {{ $cleaner->cleaner_name }}">
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i> {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                - N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned By:</strong>
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="empty-state">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> 
                            There are currently no completed complaints older than this week.
                        </p>
                    @endforelse

                    <!-- Load More Button -->
                    @if($completedOlder->hasMorePages())
                        <div class="load-more-container completed-load-more">
                            <button class="btn-load-more" data-status="completed" data-filter="older" data-page="{{ $completedOlder->currentPage() + 1 }}">
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hidden Template for Complaint Card -->
            <template id="complaint-card-template">
                <div class="card fade-in" data-date="">
                    <div class="card-header">
                        <h3></h3>
                        <span class="status badge-ongoing">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="description"></p>
                        <button class="btn-details" data-id="" aria-expanded="false" aria-controls="">View Details</button>
                        <div id="" class="toggle-content">
                            <div class="details-container">
                                <div class="detail-item">
                                    <strong>Complaint By:</strong>
                                    <span></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Complaint Date:</strong>
                                    <span></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Assigned Date:</strong>
                                    <span></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Assigned Cleaners:</strong>
                                    <span></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Assigned By:</strong>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Notification Area -->
            <div id="notification" class="notification hidden">
                <span id="notification-message"></span>
            </div>
        </div>
    @endsection

    

    @push('scripts')
    @vite([
        'resources/supervisor/app.js',
        'resources/supervisor/dashboard.js',
        'resources/supervisor/history.js',
    ])
    @endpush
