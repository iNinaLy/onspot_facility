@extends('layouts.app')

@section('title', 'Cleaners Management')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @push('styles')
<style>
  /* General Body and Container Styling */
  body {
    background-color: #f3f6f9;
    font-family: 'Helvetica Neue', Arial, sans-serif;
    color: #2E5675;
  }

  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  /* Header with Filters on the Right */
  .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 3rem;
  }

  .heading {
    font-size: 2rem;
    font-weight: 700;
    color: #2E5675;
    margin-top: 3rem;
  }

  /* Section Heading */
  .section-heading {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2E5675;
    margin: 2rem 0 1rem;
    border-bottom: 2px solid #d1d5db;
    padding-bottom: 0.5rem;
  }

  /* Filter Form Styles */
  .filter-container, .filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .filter-container select,
  .filter-container input,
  .filter-select,
  .date-input {
    padding: 0.5rem;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    font-size: 0.875rem;
    color: #555;
    width: 150px;
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .search-container {
    position: relative;
    flex-grow: 1;
    border-radius: 0.5rem;
  }

  .search-input {
    width: 100%;
    padding: 0.6rem 1.5rem 0.6rem 2.5rem;
    border-radius: 8px;
    background-color: #fff;
    font-size: 0.9rem;
    font-weight: 500;
    color: #555;
    border: 1px solid #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
    right: 0.8rem;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #888;
  }

  /* Buttons */
  .view-details-btn,
  .edit-btn,
  .btn-toggle-all {
    background-color: #2E5675;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .view-details-btn:hover,
  .edit-btn:hover,
  .btn-toggle-all:hover {
    background-color: #1f3c52;
  }

  /* Card Styles */
  .complaint-card, .list-item {
    background-color: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    margin-bottom: 1.5rem;
  }

  .complaint-card:hover, .list-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
  }

  .complaint-image-container {
    flex: 0 0 220px;
    height: 220px;
    background-color: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .complaint-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .complaint-details {
    flex-grow: 1;
    padding: 1.5rem;
  }

  .list-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .list-item-title, .complaint-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 0.5rem;
  }

  .list-item-date, .complaint-meta {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
  }

  .toggle-content {
    display: none;
    padding: 1.5rem;
    font-size: 1rem;
    color: #555;
    background-color: #f9fafb;
    border-top: 1px solid #e0e0e0;
    margin-top: 0.75rem;
    border-radius: 0 0 12px 12px;
  }

  /* Status Badge Styles */
  .complaint-status, .badge {
    font-size: 0.85rem;
    padding: 0.4em 0.7em;
    border-radius: 9999px;
    font-weight: 600;
    text-transform: capitalize;
  }

  .status-pending {
    background-color: #fee2e2;
    color: #b91c1c;
  }

  .status-ongoing {
    background-color: #fef3c7;
    color: #ca8a04;
  }

  .status-completed {
    background-color: #d1fae5;
    color: #065f46;
  }

  /* New Badge */
  .new-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #cce5ff;
    color: #004085;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: bold;
    border-radius: 0.5rem;
  }

  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
  }

  .pagination li {
    margin: 0 5px;
  }

  .pagination a, .pagination span {
    color: #2E5675;
    padding: 8px 12px;
    text-decoration: none;
    border: 1px solid #d1d5db;
    border-radius: 5px;
  }

  .pagination .active span {
    background-color: #2E5675;
    color: white;
    border-color: #2E5675;
  }

  .pagination a:hover {
    background-color: #f0f0f0;
  }
</style>
@endpush


@section('content')
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

    <!-- Section for Ongoing Complaints -->
    <div class="section">
        <h2 class="section-heading">Ongoing Complaints</h2>
        @forelse($ongoingComplaints as $complaint)
            <div class="list-item" data-status="{{ $complaint->comp_status }}" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                <div class="list-item-header">
                    <div class="list-item-title">Location: {{ $complaint->comp_location ?? 'N/A' }}</div>
                    <small class="list-item-date">{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</small>
                </div>
                <div><strong>Description:</strong> {{ $complaint->comp_desc }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="badge status-ongoing">Ongoing</span>
                </div>
                <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                <div id="details-{{ $complaint->id }}" class="toggle-content">
                    <p><strong>Complaint by:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                    <h6 class="mt-3">Assigned Cleaners:</h6>
                    <ul class="cleaners-list">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li><strong>{{ $cleaner->cleaner_name }}</strong></li>
                            <li>
                                <div class="phone-link">
                                    <i class="fas fa-phone-alt"></i> {{ $cleaner->cleaner_phoneNo ?? 'N/A' }}
                                </div>
                            </li>
                        @empty
                            <li>No cleaners assigned.</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <p><strong>Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') ?? 'N/A' }}</p>
                        <p><strong>Assigned Time:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p>No ongoing complaints found.</p>
        @endforelse
    </div>

    <!-- Section for Completed Complaints -->
    <div class="section">
        <h2 class="section-heading">Completed Complaints</h2>
        @forelse($completedComplaints as $complaint)
            <div class="list-item" data-status="{{ $complaint->comp_status }}" data-description="{{ strtolower($complaint->comp_desc) }}" data-date="{{ $complaint->comp_date }}">
                <div class="list-item-header">
                    <div class="list-item-title">Location: {{ $complaint->comp_location ?? 'N/A' }}</div>
                    <small class="list-item-date">{{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</small>
                </div>
                <div><strong>Description:</strong> {{ $complaint->comp_desc }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="badge status-completed">Completed</span>
                </div>
                <button class="btn-details" onclick="toggleDetails({{ $complaint->id }})">View Details</button>
                <div id="details-{{ $complaint->id }}" class="toggle-content">
                    <p><strong>Complaint by:</strong> {{ $complaint->officer->name ?? 'Unknown Officer' }}</p>
                    <h6 class="mt-3">Assigned Cleaners:</h6>
                    <ul class="cleaners-list">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li><strong>{{ $cleaner->cleaner_name }}</strong></li>
                            <li>
                                <div class="phone-link">
                                    <i class="fas fa-phone-alt"></i> {{ $cleaner->cleaner_phoneNo ?? 'N/A' }}
                                </div>
                            </li>
                        @empty
                            <li>No cleaners assigned.</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <p><strong>Assigned Date:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('d M Y') ?? 'N/A' }}</p>
                        <p><strong>Assigned Time:</strong> {{ \Carbon\Carbon::parse($complaint->assigned_date)->format('h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p>No completed complaints found.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
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
@endpush
