{{-- resources/views/supervisor/cleaners/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Cleaners')

@push('styles')
@vite('resources/supervisor/cleaner.css')
@endpush

@section('content')
<div class="container">
    <!-- Page Heading -->
    <div class="header-container">
        <h1 class="heading">Cleaners</h1>
    </div>

    <!-- Cleaner Overview (Top Stats) -->
    <div class="cleaner-overview">
        <div class="overview-box">
            <div class="overview-number">{{ $totalCleaners }}</div>
            <div class="overview-label">Total Cleaners</div>
        </div>
        <div class="overview-box">
            <div class="overview-number">{{ $availableCount }}</div>
            <div class="overview-label">Available</div>
        </div>
        <div class="overview-box">
            <div class="overview-number">{{ $unavailableCount }}</div>
            <div class="overview-label">Unavailable</div>
        </div>
    </div>

    <!-- Tabs + Search -->
    <div class="tabs-and-search">
        <div class="tab-header">
            <button class="tab-btn active" data-tab="available-panel">Available</button>
            <button class="tab-btn" data-tab="unavailable-panel">Unavailable</button>
        </div>

        <form class="filter-container" action="{{ route('supervisor.cleaners') }}" method="GET">
            <input 
                type="search" 
                name="search"
                placeholder="Search for cleaners..." 
                aria-label="Search Cleaners"
                value="{{ request('search') }}"
            >
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- ======================= AVAILABLE CLEANERS ======================= -->
        <div class="tab-panel active" id="available-panel">
            <table class="cleaner-list" aria-label="Available Cleaners">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Building</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($availableCleaners as $cleaner)
                    <tr class="cleaner-row">
                        <td>
                            @if($cleaner->profile_pic)
                                <img 
                                    src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                    alt="{{ $cleaner->cleaner_name }}'s Profile"
                                    class="cleaner-profile-pic"
                                >
                            @else
                                <div class="no-image-placeholder">No Image</div>
                            @endif
                        </td>
                        <td>
                            <span class="cleaner-name">{{ $cleaner->cleaner_name }}</span>
                        </td>
                        <td>{{ $cleaner->building }}</td>
                        <td>{{ $cleaner->cleaner_phoneNo }}</td>
                        <td>
                            <span class="cleaner-status status-available">Available</span>
                        </td>
                        <td>
                            <button class="view-details-btn"
                                data-name="{{ $cleaner->cleaner_name }}"
                                data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : '' }}"
                                data-building="{{ $cleaner->building }}"
                                data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                                data-username="{{ $cleaner->cleaner_username }}"
                                data-status="{{ $cleaner->status }}"
                                data-complaints="{{ json_encode($cleaner->complaints->map(fn($c) => [
                                    'desc' => $c->comp_desc,
                                    'status' => $c->comp_status,
                                ])) }}"
                            >
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No available cleaners found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination for Available Cleaners -->
            @if($availableCleaners->hasPages())
                <div class="pagination-container" style="margin-top:1rem;">
                    {{ $availableCleaners->links() }}
                </div>
            @endif
        </div>

        <!-- ======================= UNAVAILABLE CLEANERS ======================= -->
        <div class="tab-panel" id="unavailable-panel">
            <table class="cleaner-list" aria-label="Unavailable Cleaners">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Building</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($unavailableCleaners as $cleaner)
                    <tr class="cleaner-row">
                        <td>
                            @if($cleaner->profile_pic)
                                <img 
                                    src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                    alt="{{ $cleaner->cleaner_name }}'s Profile"
                                    class="cleaner-profile-pic"
                                >
                            @else
                                <div class="no-image-placeholder">No Image</div>
                            @endif
                        </td>
                        <td>
                            <span class="cleaner-name">{{ $cleaner->cleaner_name }}</span>
                        </td>
                        <td>{{ $cleaner->building }}</td>
                        <td>{{ $cleaner->cleaner_phoneNo }}</td>
                        <td>
                            <span class="cleaner-status status-unavailable">Unavailable</span>
                        </td>
                        <td>
                            <button class="view-details-btn"
                                data-name="{{ $cleaner->cleaner_name }}"
                                data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : '' }}"
                                data-building="{{ $cleaner->building }}"
                                data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                                data-username="{{ $cleaner->cleaner_username }}"
                                data-status="{{ $cleaner->status }}"
                                data-complaints="{{ json_encode($cleaner->complaints->map(fn($c) => [
                                    'desc' => $c->comp_desc,
                                    'status' => $c->comp_status,
                                ])) }}"
                            >
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No unavailable cleaners found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination for Unavailable Cleaners -->
            @if($unavailableCleaners->hasPages())
                <div class="pagination-container" style="margin-top:1rem;">
                    {{ $unavailableCleaners->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========== MODAL ========== -->
<div id="cleanerModal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content">
        <span class="close-button" aria-label="Close Modal">&times;</span>
        <div class="modal-body">
            <!-- Profile Pic / No Image Placeholder -->
            <img id="modal-profile-pic" class="modal-profile-pic" alt="Cleaner Profile">
            <div class="modal-no-image-placeholder" id="modal-no-image-placeholder">No Image</div>

            <!-- Basic Info -->
            <h2 class="modal-cleaner-name" id="modal-cleaner-name"></h2>
            <span class="modal-cleaner-username" id="modal-cleaner-username"></span>
            <div id="modal-cleaner-status-container"></div>  <!-- Container for dynamic status -->

            <!-- Additional Info -->
            <div class="modal-cleaner-details">
                <p>
                    <strong>Phone:</strong>
                    <a href="#" id="modal-cleaner-phoneNo" target="_blank" rel="noopener noreferrer">N/A</a>
                </p>
                <p>
                    <strong>Username:</strong>
                    <span id="modal-cleaner-username-detail">N/A</span>
                </p>
                <p>
                    <strong>Building:</strong>
                    <span id="modal-cleaner-building">N/A</span>
                </p>
            </div>

            <!-- Complaints Section -->
            <div class="assigned-complaints">
                <h3>Assigned Complaints:</h3>
                <div id="complaint-spinner">
                    <i class="fas fa-spinner fa-spin loader"></i> Loading...
                </div>
                <ul id="modal-cleaner-complaints"></ul>
                <p id="complaint-message">Cleaner still has ongoing tasks.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
    'resources/supervisor/cleaner.js',
])
@endpush
