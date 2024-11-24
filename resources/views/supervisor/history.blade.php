{{-- resources/views/history.blade.php --}}

@extends('layouts.app')

@section('title', 'Complaint History')

@push('styles')
    <style>
        /* General Styling */
        body {
            background-color: #f2f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .heading {
            font-size: 2em;
            font-weight: 700;
            margin-top: 4rem;
            margin-bottom: 2rem;
            color: #2e5675;
            text-align: left;
        }

        .section-heading {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2e3a59;
            margin: 2rem 0 1rem;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }

        .sub-heading {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2e3a59;
            margin: 1rem 0;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .filter-bar input {
            flex: 1 1 300px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            transition: border-color 0.3s;
        }

        .filter-bar input:focus {
            border-color: #2e3a59;
            outline: none;
        }

        /* Button Group */
        .btn-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .btn-group button {
            padding: 0.5rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-group button.active {
            background-color: #2e5675;
            color: #fff;
        }

        .btn-group button.inactive {
            background-color: #e0e0e0;
            color: #2e3a59;
        }

        .btn-group button:hover {
            opacity: 0.9;
        }

        /* Card Styles */
        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2e3a59;
            margin: 0;
        }

        .card-header .status {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px; /* Pill shape */
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, color 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Status Badge Colors */
        .badge-completed {
            background-color: #d4edda; /* Light Green */
            color: #155724; /* Dark Green Text */
            border: 1px solid #c3e6cb;
        }

        .badge-ongoing {
            background-color: #d1ecf1; /* Light Blue */
            color: #0c5460; /* Dark Blue Text */
            border: 1px solid #bee5eb;
        }

        .description {
            font-size: 1rem;
            margin: 0.5rem 0 1rem;
            color: #555;
            line-height: 1.5;
        }

        /* Buttons */
        .btn-details {
            padding: 0.3rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 600;
            background-color: #2e5675;
            color: #fff;
            border: solid;
            border-radius: 6rem;
            cursor: pointer;
            transition: background-color 0.3s, opacity 0.3s;
        }

        .btn-details:hover {
            background-color: #1c3d55;
            opacity: 0.9;
        }

        /* Details Section */
        .toggle-content {
            display: none;
            padding-top: 1rem;
            border-top: 1px solid #e0e0e0;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.6;
        }

        .toggle-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        .toggle-content p {
            margin: 0.5rem 0;
        }

        .toggle-content h6 {
            margin-top: 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: #2e3a59;
        }

        .toggle-content ul {
            list-style: disc inside;
            padding-left: 1rem;
        }

        .toggle-content ul li {
            margin-bottom: 0.5rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            color: #555;
            margin: 2rem 0;
            font-size: 1rem;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .empty-state-completed {
            text-align: center;
            color: #0c5460; /* Dark Teal for professionalism */
            background-color: #d1ecf1; /* Light Blue Background for a calm tone */
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #bee5eb;
            margin: 2rem 0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .empty-state-completed i {
            color: #0c5460; /* Match the text color */
            font-size: 1.2rem;
        }

        .empty-state-ongoing {
            text-align: center;
            color: #555;
            margin: 2rem 0;
            font-size: 1rem;
            font-style: italic;
        }

        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            padding: 1.5rem 0;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            display: block;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            color: #2e3a59;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination .page-link:hover {
            background-color: #2e3a59;
            color: #fff;
        }

        .pagination .active .page-link {
            background-color: #2e3a59;
            color: #fff;
            border-color: #2e3a59;
        }

        .pagination .disabled .page-link {
            color: #ccc;
            pointer-events: none;
            background-color: #f2f4f8;
            border-color: #ccc;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-group button {
                width: 100%;
            }
        }

        /* Additional Styling for Phone Links */
        .phone-link {
            color: #0c5460; /* Dark Teal for consistency */
            text-decoration: none;
            transition: color 0.3s;
        }

        .phone-link:hover {
            color: #155724; /* Slightly darker on hover */
        }

        .phone-link i {
            margin-right: 0.3rem;
        }

        /* Styling for Complaint Date */
        .complaint-date {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.5rem;
            color: #555;
            font-size: 0.95rem;
        }

        .complaint-date i {
            color: #2e3a59;
        }
    </style>
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
                    <p class="empty-state empty-state-ongoing">There are no ongoing complaints at the moment.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to toggle the visibility of the details section
        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            details.classList.toggle('active');
        }

        // Button Group Functionality
        const showTodayBtn = document.getElementById('showTodayBtn');
        const showThisWeekBtn = document.getElementById('showThisWeekBtn');
        const showOlderBtn = document.getElementById('showOlderBtn');

        showTodayBtn.addEventListener('click', function() {
            document.getElementById('completedToday').style.display = 'block';
            document.getElementById('completedThisWeek').style.display = 'none';
            document.getElementById('completedOlder').style.display = 'none';

            toggleActive(this, [showThisWeekBtn, showOlderBtn]);
        });

        showThisWeekBtn.addEventListener('click', function() {
            document.getElementById('completedToday').style.display = 'none';
            document.getElementById('completedThisWeek').style.display = 'block';
            document.getElementById('completedOlder').style.display = 'none';

            toggleActive(this, [showTodayBtn, showOlderBtn]);
        });

        showOlderBtn.addEventListener('click', function() {
            document.getElementById('completedToday').style.display = 'none';
            document.getElementById('completedThisWeek').style.display = 'none';
            document.getElementById('completedOlder').style.display = 'block';

            toggleActive(this, [showTodayBtn, showThisWeekBtn]);
        });

        // Function to toggle active/inactive button states
        function toggleActive(activeBtn, otherButtons) {
            activeBtn.classList.remove('inactive');
            activeBtn.classList.add('active');

            otherButtons.forEach(button => {
                button.classList.remove('active');
                button.classList.add('inactive');
            });
        }

        // Search Functionality Applied to All Complaints
        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const allCards = document.querySelectorAll('.card'); // Select all cards in Completed and Ongoing sections

            allCards.forEach(card => {
                const description = card.dataset.description || ''; // Use the dataset description
                card.style.display = description.includes(searchTerm) ? 'block' : 'none';
            });

            // Handle empty state messages if no cards are visible
            const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
                .some(card => card.style.display === 'block');
            const ongoingVisible = Array.from(document.querySelectorAll('#ongoingComplaints .card'))
                .some(card => card.style.display === 'block');

            // Show or hide empty state messages
            const emptyStateCompleted = document.querySelector('.empty-state-completed');
            const emptyStateOngoing = document.querySelector('.empty-state-ongoing');

            if (emptyStateCompleted) {
                emptyStateCompleted.style.display = completedVisible ? 'none' : 'flex';
            }

            if (emptyStateOngoing) {
                emptyStateOngoing.style.display = ongoingVisible ? 'none' : 'block';
            }
        });

        // Date Filter Functionality Applied to All Complaints
        document.getElementById('dateFilter').addEventListener('change', function () {
            const selectedDate = this.value;
            const allCards = document.querySelectorAll('.card'); // Select all cards in Completed and Ongoing sections

            allCards.forEach(card => {
                const date = card.dataset.date || '';
                if (selectedDate) {
                    card.style.display = (date === selectedDate) ? 'block' : 'none';
                } else {
                    // If no date is selected, show all cards
                    card.style.display = 'block';
                }
            });

            // Handle empty state messages if no cards are visible
            const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
                .some(card => card.style.display === 'block');
            const ongoingVisible = Array.from(document.querySelectorAll('#ongoingComplaints .card'))
                .some(card => card.style.display === 'block');

            // Show or hide empty state messages
            const emptyStateCompleted = document.querySelector('.empty-state-completed');
            const emptyStateOngoing = document.querySelector('.empty-state-ongoing');

            if (emptyStateCompleted) {
                emptyStateCompleted.style.display = completedVisible ? 'none' : 'flex';
            }

            if (emptyStateOngoing) {
                emptyStateOngoing.style.display = ongoingVisible ? 'none' : 'block';
            }
        });
    </script>
@endpush
