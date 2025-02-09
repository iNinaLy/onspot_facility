{{-- resources/views/supervisor/history.blade.php --}}
@extends('layouts.app')
@section('title', 'Complaint History')

@section('content')
    <div class="container">
        <h1 class="heading">History</h1>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <!-- 1) Search bar (for client-side + optional server-side) -->
            <input type="text" id="searchInput" placeholder="Search complaints...">

            <!-- 2) Date filter (client-side show/hide based on assigned_date) -->
            <input type="date" id="dateFilter">

            <!-- 3) "Assigned By Me" toggle -->
            <button
                id="assignedByMeFilter"
                class="btn-filter"
                data-filter="{{ $assignedByMe ? 'false' : 'true' }}"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ $assignedByMe ? 'Display all complaints' : 'Display complaints assigned by you' }}"
            >
                {{ $assignedByMe ? 'All Complaints' : 'Assigned by me' }}
            </button>
        </div>

        <!-- Main Tab Navigation (Ongoing vs Completed) -->
        <div class="tab-navigation">
            <button class="active" data-tab="ongoing">Ongoing Complaints</button>
            <button class="inactive" data-tab="completed">Completed Complaints</button>
        </div>

        <!-- Tab Content -->
        <div id="tab-content" class="tab-content">
            <!-- ===================================== -->
            <!-- Ongoing Complaints Tab -->
            <!-- ===================================== -->
            <div class="tab-pane" id="ongoing" style="display: block;">
                <!-- Sub-Tab Navigation (Timeframe) -->
                <div class="btn-group">
                    <button class="active" data-filter="today">Today</button>
                    <button class="inactive" data-filter="thisWeek">This Week</button>
                    <button class="inactive" data-filter="older">Older</button>
                </div>

                <!-- ===== Ongoing Today ===== -->
                <div class="time-category" id="ongoingToday">
                    <h3 class="sub-heading">Today</h3>
                    @forelse($ongoingToday as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <!-- Brief Description -->
                                <p class="description">{{ $complaint->comp_desc }}</p>

                                <!-- "View Details" toggle -->
                                <button class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>

                                <!-- Complaint Details (hidden until toggled) -->
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <!-- Complaint By -->
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>

                                        <!-- Complaint Date -->
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Assigned Date -->
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Assigned Cleaners -->
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
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                    {{ $cleaner->cleaner_phoneNo }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>

                                        <!-- Assigned By -->
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

                <!-- ===== Ongoing This Week ===== -->
                <div class="time-category" id="ongoingThisWeek" style="display: none;">
                    <h3 class="sub-heading">This Week</h3>
                    @forelse($ongoingThisWeek as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button 
                                    class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <!-- (Same structure as above) -->
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
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
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                    {{ $cleaner->cleaner_phoneNo }}
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

                <!-- ===== Ongoing Older ===== -->
                <div class="time-category" id="ongoingOlder" style="display: none;">
                    <h3 class="sub-heading">Older</h3>
                    @forelse($ongoingOlder as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-ongoing">
                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button 
                                    class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <!-- (Same structure) -->
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
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
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                    {{ $cleaner->cleaner_phoneNo }}
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

                    <!-- "Load More" for Ongoing Older -->
                    @if($ongoingOlder->hasMorePages())
                        <div class="load-more-container ongoing-load-more">
                            <button class="btn-load-more" 
                                data-status="ongoing" 
                                data-filter="older" 
                                data-page="{{ $ongoingOlder->currentPage() + 1 }}"
                            >
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div> <!-- END ongoing tab-pane -->

            <!-- ===================================== -->
            <!-- Completed Complaints Tab -->
            <!-- ===================================== -->
            <div class="tab-pane" id="completed" style="display: none;">
                <!-- Sub-Tab Navigation (Timeframe) -->
                <div class="btn-group">
                    <button class="active" data-filter="today">Today</button>
                    <button class="inactive" data-filter="thisWeek">This Week</button>
                    <button class="inactive" data-filter="older">Older</button>
                </div>

                <!-- ===== Completed Today ===== -->
                <div class="time-category" id="completedToday">
                    <h3 class="sub-heading">Today</h3>
                    @forelse($completedToday as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button 
                                    class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
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
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                    {{ $cleaner->cleaner_phoneNo }}
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

                <!-- ===== Completed This Week ===== -->
                <div class="time-category" id="completedThisWeek" style="display: none;">
                    <h3 class="sub-heading">This Week</h3>
                    @forelse($completedThisWeek as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button 
                                    class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <!-- same structure -->
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
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
                                                                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                    {{ $cleaner->cleaner_phoneNo }}
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

                <!-- ===== Completed Older ===== -->
                <div class="time-category" id="completedOlder" style="display: none;">
                    <h3 class="sub-heading">Older</h3>
                    @forelse($completedOlder as $complaint)
                        <div 
                            class="card fade-in complaint-card"
                            data-date="{{ $complaint->assigned_date }}"
                            data-desc="{{ \Illuminate\Support\Str::lower($complaint->comp_desc ?? '') }}"
                            data-loc="{{ \Illuminate\Support\Str::lower($complaint->comp_location ?? '') }}"
                        >
                            <div class="card-header">
                                <h3>{{ $complaint->comp_location ?? 'N/A' }}</h3>
                                <span class="status badge-completed">
                                    <i class="fas fa-check-circle" aria-hidden="true"></i> Completed
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="description">{{ $complaint->comp_desc }}</p>
                                <button 
                                    class="btn-details" 
                                    data-id="{{ $complaint->id }}" 
                                    aria-expanded="false" 
                                    aria-controls="details-{{ $complaint->id }}"
                                >
                                    View Details
                                </button>
                                <div id="details-{{ $complaint->id }}" class="toggle-content">
                                    <div class="details-container">
                                        <!-- same structure -->
                                        <div class="detail-item">
                                            <strong>Complaint By:</strong>
                                            <span>{{ $complaint->officer->name ?? 'Unknown Officer' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Complaint Date:</strong>
                                            <span>
                                                @if($complaint->comp_date)
                                                    {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <strong>Assigned Date:</strong>
                                            <span>
                                                @if($complaint->assigned_date)
                                                    {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
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
                                            <span>{{ $complaint->supervisor->name ?? 'Unknown Supervisor' }}</span>
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

                    <!-- "Load More" for Completed Older -->
                    @if($completedOlder->hasMorePages())
                        <div class="load-more-container completed-load-more">
                            <button class="btn-load-more"
                                data-status="completed"
                                data-filter="older"
                                data-page="{{ $completedOlder->currentPage() + 1 }}"
                            >
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div> <!-- END completed tab-pane -->

            <!-- Hidden Template for new complaint cards (used by AJAX "Load More") -->
            <template id="complaint-card-template">
                <div class="card fade-in" data-date="">
                    <div class="card-header">
                        <h3></h3>
                        <!-- By default, set it to "ongoing" style; we can swap it in JS if completed -->
                        <span class="status badge-ongoing">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="description"></p>
                        <button class="btn-details" data-id="" aria-expanded="false" aria-controls="">
                            View Details
                        </button>
                        <div id="" class="toggle-content">
                            <div class="details-container">
                                <!-- detail-item #1: Complaint By -->
                                <div class="detail-item">
                                    <strong>Complaint By:</strong>
                                    <span></span>
                                </div>
                                <!-- detail-item #2: Complaint Date -->
                                <div class="detail-item">
                                    <strong>Complaint Date:</strong>
                                    <span></span>
                                </div>
                                <!-- detail-item #3: Assigned Date -->
                                <div class="detail-item">
                                    <strong>Assigned Date:</strong>
                                    <span></span>
                                </div>
                                <!-- detail-item #4: Cleaners -->
                                <div class="detail-item">
                                    <strong>Assigned Cleaners:</strong>
                                    <span></span>
                                </div>
                                <!-- detail-item #5: Assigned By -->
                                <div class="detail-item">
                                    <strong>Assigned By:</strong>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Notification area (hidden by default) -->
            <div id="notification" class="notification hidden">
                <span id="notification-message"></span>
            </div>
        </div> <!-- END tab-content -->
    </div> <!-- END container -->
@endsection

@push('scripts')
    @vite([
        'resources/supervisor/app.js',
        'resources/supervisor/dashboard.js',
        'resources/supervisor/history.js',
    ])

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterButton = document.getElementById('assignedByMeFilter');
            filterButton.addEventListener('click', () => {
                const url = new URL(window.location);
                // Toggle the 'assigned_by_me' query parameter
                if (url.searchParams.has('assigned_by_me')) {
                    url.searchParams.delete('assigned_by_me');
                } else {
                    url.searchParams.set('assigned_by_me', 'true');
                }
                window.location.href = url.toString();
            });
        });
    </script>
@endpush
