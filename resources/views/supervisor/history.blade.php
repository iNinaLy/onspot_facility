<x-app-layout>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Body and Container Styling */
        body {
            background-color: #f3f6f9;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #2E5675;
        }
        .container {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 1rem;
        }
        .heading {
            font-size: 2rem;
            font-weight: 700;
            color: #2E5675;
            text-align: left;
            margin-bottom: 2rem;
        }
        .section-heading {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2E5675;
            margin: 2rem 0 1rem;
            border-bottom: 2px solid #d1d5db;
            padding-bottom: 0.5rem;
        }
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .search-container {
            position: relative;
            flex-grow: 1;
        }
        .search-input {
            width: 100%;
            padding: 0.6rem 1.5rem 0.6rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
            transition: border-color 0.2s ease;
        }
        .search-input:focus {
            outline: none;
            border-color: #2E5675;
            box-shadow: 0 2px 8px rgba(46, 86, 117, 0.2);
        }
        .search-icon {
            position: absolute;
            top: 50%;
            left: 0.8rem;
            transform: translateY(-50%);
            font-size: 1rem;
            color: #888;
        }
        .filter-select, .btn-toggle-all, .date-input {
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.4rem 1.5rem;
            color: #555;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn-toggle-all {
            background-color: #2E5675;
            color: #fff;
        }
        .btn-toggle-all:hover {
            background-color: #1f3c52;
        }
        /* Remaining Styles */
        .list-item {
            background-color: #ffffff;
            border: 1px solid #e1e5ea;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .list-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .list-item-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #2E5675;
        }
        .list-item-date {
            font-size: 0.85rem;
            color: #6b7280;
        }
        /* Badge Styling */
        .badge {
            font-size: 0.85rem;
            padding: 0.4em 0.7em;
            border-radius: 5px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending { background-color: #fce8e8; color: #c0392b; }
        .status-ongoing { background-color: #fff5db; color: #e67e22; }
        .status-completed { background-color: #e8f5e9; color: #27ae60; }
        .badge-new { background-color: #cce5ff; color: #004085; }
        .btn-details {
            background-color: #2E5675;
            color: white;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.2s ease;
        }
        .btn-details:hover { background-color: #1f3c52; }
        /* Toggle Content */
        .toggle-content {
            padding: 1.5rem;
            font-size: 1rem;
            color: #555;
            background-color: #f9fafb;
            border-top: 1px solid #e0e0e0;
            margin-top: 0.75rem;
            border-radius: 0 0 12px 12px;
            display: none;
        }
        .cleaners-list {
            list-style-type: none;
            padding: 0;
            margin-top: 0.5rem;
        }
        .cleaners-list li {
            color: #2E5675;
            margin-bottom: 10px;
        }
        /* Phone Link Styling */
        .phone-link {
            display: inline-flex;
            align-items: center;
            color: rgba(46, 86, 117, 0.6); /* Low opacity color */
            font-size: 0.85rem; /* Smaller font size */
            text-decoration: none;
            margin-top: 4px; /* Adds space under cleaner's name */
        }
        .phone-link i {
            margin-right: 4px;
            color: rgba(39, 174, 96, 0.6); /* Icon color with low opacity */
        }
        .phone-link:hover {
            color: rgba(31, 60, 82, 0.8); /* Slightly darker color on hover */
        }
    </style>
</head>

<div class="container">
    <h1 class="heading">Complaint History</h1>

    <!-- Filter and Expand/Collapse All Buttons -->
    <div class="filter-bar">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Search complaints by description...">
        </div>
        <input type="date" id="dateFilter" class="date-input" placeholder="Select date">
        <select id="statusFilter" class="filter-select">
            <option value="all">All Statuses</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
        </select>
        <button id="toggleAllBtn" class="btn-toggle-all">Expand All</button>
    </div>

    <!-- Complaints Sections (Today's and Past Complaints) -->
    <div class="section">
        <h2 class="section-heading">Recent Complaints</h2>
        @forelse($todaysComplaints as $complaint)
            <div class="list-item" data-status="{{ $complaint->comp_status }}" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                <div class="list-item-header">
                    <div class="list-item-title">Location: {{ $complaint->comp_location ?? 'N/A' }}</div>
                    <small class="list-item-date">{{ \Carbon\Carbon::parse($complaint->comp_date)->format('H:i A') }}</small>
                    @if($complaint->updated_at->diffInHours(now()) <= 24)
                        <span class="badge badge-new">New</span>
                    @endif
                </div>
                <div><strong>Description:</strong> {{ $complaint->comp_desc }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="badge 
                      {{ strtolower($complaint->comp_status) == 'pending' ? 'status-pending' : '' }}
                      {{ strtolower($complaint->comp_status) == 'ongoing' ? 'status-ongoing' : '' }}
                      {{ strtolower($complaint->comp_status) == 'completed' ? 'status-completed' : '' }}">
                        {{ ucfirst($complaint->comp_status) }}
                    </span>
                </div>
                <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                <div id="details-{{ $complaint->id }}" class="toggle-content">
                    <p><strong>Complaint by:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                    <h6 class="mt-3">Assigned Cleaners:</h6>
                    <ul class="cleaners-list">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li>
                                <strong>{{ $cleaner->cleaner_name }}</strong>
                                <div class="phone-link">
                                    <i class="fas fa-phone-alt"></i> {{ $cleaner->cleaner_phoneNo ?? 'N/A' }}
                                </div>
                            </li>
                        @empty
                            <li>No cleaners assigned.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <p>No recent complaints.</p>
        @endforelse
    </div>

    <div class="section">
        <h2 class="section-heading">Past Complaints</h2>
        @forelse($pastComplaints as $complaint)
            <div class="list-item" data-status="{{ $complaint->comp_status }}" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                <div class="list-item-header">
                    <div class="list-item-title">Floor: {{ $complaint->comp_location ?? 'N/A' }}</div>
                    <small class="list-item-date">{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</small>
                </div>
                <div><strong>Description:</strong> {{ $complaint->comp_desc }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="badge 
                      {{ strtolower($complaint->comp_status) == 'pending' ? 'status-pending' : '' }}
                      {{ strtolower($complaint->comp_status) == 'ongoing' ? 'status-ongoing' : '' }}
                      {{ strtolower($complaint->comp_status) == 'completed' ? 'status-completed' : '' }}">
                        {{ ucfirst($complaint->comp_status) }}
                    </span>
                </div>
                <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                <div id="details-{{ $complaint->id }}" class="toggle-content">
                    <p><strong>Complaint by:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                    <h6 class="mt-3">Assigned Cleaners:</h6>
                    <ul class="cleaners-list">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li>
                                <strong>{{ $cleaner->cleaner_name }}</strong>
                            </li>
                            <li>
                            <div class="phone-link">
                                    <i class="fas fa-phone-alt"></i> {{ $cleaner->cleaner_phoneNo ?? 'N/A' }}
                            </div>
                             </li>
                        @empty
                            <li>No cleaners assigned.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <p>No past complaints found.</p>
        @endforelse
    </div>
</div>

<!-- Custom JavaScript for filter, toggle, and search functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleDetails(id) {
        $('#details-' + id).toggle();
    }

    let expanded = false;
    $('#toggleAllBtn').click(function() {
        expanded = !expanded;
        $('.toggle-content').toggle(expanded);
        $(this).text(expanded ? 'Collapse All' : 'Expand All');
    });

    $('#statusFilter').change(function() {
        let selectedStatus = $(this).val();
        $('.list-item').each(function() {
            let status = $(this).data('status');
            $(this).toggle(selectedStatus === 'all' || status === selectedStatus);
        });
    });

    $('#dateFilter').change(function() {
        let selectedDate = $(this).val();
        $('.list-item').each(function() {
            let date = $(this).data('date');
            $(this).toggle(date.startsWith(selectedDate));
        });
    });

    $('#searchInput').on('input', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('.list-item').each(function() {
            let description = $(this).data('description');
            $(this).toggle(description.includes(searchTerm));
        });
    });
</script>
</x-app-layout>
