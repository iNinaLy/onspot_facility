@extends('layouts.app')

@section('title', 'Cleaners')

@push('styles')
    <!-- External Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
<div class="container">
    <div class="header-container">
        <h1 class="heading">Cleaners</h1>
        <form id="filter-form" class="filter-container">
            <input 
                type="search" 
                class="search-input" 
                placeholder="Search for cleaners..." 
                aria-label="Search" 
                id="search-input" 
            />
            <select id="sort-status" class="sort-select">
                <option value="all">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <select id="sort-by" class="sort-select">
                <option value="default">Sort By</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="status_asc">Status (Available First)</option>
                <option value="status_desc">Status (Unavailable First)</option>
            </select>
        </form>
    </div>

    <div class="cleaner-overview">
        <div class="overview-box overview-total">
            <div class="overview-number">{{ $totalCleaners }}</div>
            <div class="overview-label">Total Cleaners</div>
        </div>
        <div class="overview-box overview-available">
            <div class="overview-number">{{ $availableCount }}</div>
            <div class="overview-label">Available</div>
        </div>
        <div class="overview-box overview-unavailable">
            <div class="overview-number">{{ $unavailableCount }}</div>
            <div class="overview-label">Unavailable</div>
        </div>
    </div>

    <!-- Cleaner List -->
    <table class="cleaner-list" id="cleaner-list">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Building</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cleaners as $cleaner)
            <tr class="cleaner-row" 
                data-name="{{ strtolower($cleaner->cleaner_name) }}" 
                data-status="{{ strtolower($cleaner->status) }}" 
                data-id="{{ $cleaner->id }}"
                data-phone="{{ $cleaner->cleaner_phoneNo }}">
                <td>
                    @if($cleaner->profile_pic)
                        <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" alt="Cleaner Profile Picture" class="cleaner-profile-pic" loading="lazy">
                    @else
                        <img src="{{ asset('images/default-placeholder.png') }}" alt="Profile Picture" class="cleaner-profile-pic" loading="lazy">
                    @endif
                </td>
                <td>
                    <span class="cleaner-name">{{ $cleaner->cleaner_name }}</span>
                </td>
                <td>
                    {{ $cleaner->cleaner_phoneNo }}
                </td>
                <td>{{ $cleaner->building }}</td>
                <td>
                    @if($cleaner->status == 'available')
                        <span class="cleaner-status status-available">Available</span>
                    @elseif($cleaner->status == 'unavailable')
                        <span class="cleaner-status status-unavailable">Unavailable</span>
                    @endif
                </td>
                <td>
                    <button class="view-details-btn"
                            data-name="{{ $cleaner->cleaner_name }}"
                            data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : asset('images/default-placeholder.png') }}"
                            data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                            data-username="{{ $cleaner->cleaner_username }}"
                            data-building="{{ $cleaner->building }}"
                            data-status="{{ $cleaner->status }}"
                            data-complaints="{{ json_encode($cleaner->ongoingComplaints->map(function($complaint) { return ['desc' => $complaint->comp_desc, 'status' => $complaint->comp_status]; })) }}">
                        View Details
                    </button>
                </td>
            </tr>
            @endforeach

            <!-- No Cleaners Found Message -->
            <tr id="no-cleaners-message" style="display: none;">
                <td colspan="6">No cleaners found matching your criteria.</td>
            </tr>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $cleaners->links() }}
    </div>
</div>

<!-- Cleaner Details Modal -->
<div id="cleanerModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-cleaner-name" aria-describedby="modal-cleaner-details">
    <div class="modal-content">
        <span class="close-button" aria-label="Close Modal">&times;</span>
        <div class="modal-body">
            <img src="" alt="Cleaner Profile Picture" class="modal-profile-pic">
            <h2 class="modal-cleaner-name" id="modal-cleaner-name"></h2>
            <span class="modal-cleaner-username" id="modal-cleaner-username"></span>
            <p class="modal-cleaner-status"></p>
            <div class="modal-cleaner-details" id="modal-cleaner-details">
                <p>
                    <strong>Phone Number:</strong> 
                    <a href="#" class="modal-cleaner-phoneLink" target="_blank" rel="noopener noreferrer"></a>
                </p>
                <p>
                    <strong>Building:</strong> 
                    <span class="modal-cleaner-building"></span>
                </p>
            </div>

            <div class="assigned-complaints">
                <h3>Assigned Complaints:</h3>
                <ul id="modal-cleaner-complaints">
                    <!-- Assigned complaints will be injected here -->
                </ul>
                <p id="complaint-message" style="display: none; color: #555; font-size: 0.95rem;">
                    Cleaners still have ongoing tasks to be completed.
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Include compiled Supervisor JavaScript Files using Vite -->
    @vite([
        'resources/supervisor/app.js',
        'resources/supervisor/dashboard.js',
        'resources/supervisor/complaint.js',
        'resources/supervisor/cleaner.js', 
    ])
@endpush
