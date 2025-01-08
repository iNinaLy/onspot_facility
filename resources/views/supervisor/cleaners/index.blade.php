@extends('layouts.app')

@section('title', 'Cleaners')

@push('styles')
    {{-- Any fonts/icons you might need. Remove if unneeded. --}}
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* ------------------------------
           Tabs + Search CSS (unchanged)
        ------------------------------- */
        .tabs-and-search {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .tab-header {
            display: flex;
            gap: 2rem;
            border-bottom: none;
            margin-bottom: 0;
        }
        .tab-btn {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
            outline: none;
        }
        .tab-btn:hover:not(.active) {
            color: #989898;
            font-weight: 400;
        }
        .tab-btn.active {
            color: #272b2f;
        }
        /* Animated underline */
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0%;
            height: 3px;
            background-color: #53626e;
            transition: width 0.3s;
        }
        .tab-header button.active::after {
            width: 100%;
        }
        .tab-header button:hover::after {
            width: 100%;
        }

        /* Tab content transitions */
        .tab-content {
            position: relative;
        }
        .tab-panel {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .tab-panel.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* ------------------------------
           Cleaner Overview (unchanged)
        ------------------------------- */
        .cleaner-overview {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .overview-box {
            flex: 1;
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            text-align: center;
        }
        .overview-number {
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .overview-label {
            font-size: 0.95rem;
            color: #666;
        }

        /* ------------------------------
           New "Card-Style" Cleaner List
        ------------------------------- */
        .cleaner-list-container {
            display: flex;
            flex-direction: column;
            gap: 1rem; /* space between cards */
        }

        .cleaner-card {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            gap: 1rem;
        }
        /* Profile Container & Pic */
        .profile-pic-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #eee;
            overflow: hidden;
            flex-shrink: 0; /* don’t scale the pic on narrower screens */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cleaner-profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .no-image-placeholder {
            font-size: 0.8rem;
            color: #999;
            text-align: center;
        }

        /* Cleaner Info */
        .cleaner-info {
            flex: 1; /* take remaining horizontal space */
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }
        .cleaner-name {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            color: #2e5675;
        }
        .cleaner-phone,
        .cleaner-building,
        .cleaner-status-label {
            font-size: 0.9rem;
            color: #555;
        }
        .cleaner-status-label {
            font-weight: 500;
        }

        
        .view-details-btn {
            background: #2e5675;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .view-details-btn:hover {
            background: #1f3e54;
        }

        /* No Data Found */
        .no-cleaners-msg {
            font-size: 0.95rem;
            color: #666;
            text-align: center;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

    </style>
@endpush

@section('content')
<div class="container">
    <div class="header-container">
        <h1 class="heading">Cleaners</h1>
    </div>

    <!-- Cleaner Overview Section -->
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

    <!-- TABS + SEARCH BAR in one row -->
    <div class="tabs-and-search">
        <!-- Tab Header -->
        <div class="tab-header">
            <button class="tab-btn active" data-tab="available-pane">Available</button>
            <button class="tab-btn" data-tab="unavailable-pane">Unavailable</button>
        </div>

        <!-- Search Form on the right -->
        <form action="{{ route('supervisor.cleaners') }}" method="GET" style="display: flex; align-items: center;">
            <input type="text" name="search" id="search"
                   placeholder="Search cleaner..."
                   value="{{ old('search', $search ?? '') }}"
                   style="padding: 0.5rem; border-radius: 16px; border: 1px solid #ccc;">
            <button type="submit"
                    style="padding: 0.45rem 1rem; margin-left: 0.5rem; background: #2e5675; color: #fff; border-radius: 20px;">
                Search
            </button>
        </form>
    </div>

    <!-- TAB CONTENT -->
    <div class="tab-content">
        <!-- Available Tab Panel -->
        <div class="tab-panel active" id="available-pane">
            <div class="cleaner-list-container" aria-label="List of Available Cleaners">
                @forelse($availableCleaners as $cleaner)
                    <div class="cleaner-card">
                        <!-- Profile Pic Container -->
                        <div class="profile-pic-container">
                            @if($cleaner->profile_pic)
                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                     alt="{{ $cleaner->cleaner_name }}'s Profile Picture"
                                     class="cleaner-profile-pic" loading="lazy">
                            @else
                                <div class="no-image-placeholder" aria-label="No Image Available">No Image</div>
                            @endif
                        </div>

                        <!-- Cleaner Info -->
                        <div class="cleaner-info">
                            <p class="cleaner-name">{{ $cleaner->cleaner_name }}</p>
                            <p class="cleaner-phone">Phone: {{ $cleaner->cleaner_phoneNo }}</p>
                            <p class="cleaner-building">Building: {{ $cleaner->building }}</p>
                            <p class="cleaner-status-label">
                                Status:
                                <span class="cleaner-status status-available">Available</span>
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions">
                            <button class="view-details-btn"
                                data-name="{{ $cleaner->cleaner_name }}"
                                data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : 'no_image' }}"
                                data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                                data-username="{{ $cleaner->cleaner_username }}"
                                data-building="{{ $cleaner->building }}"
                                data-status="{{ $cleaner->status }}"
                                data-complaints="{{ json_encode($cleaner->complaints->map(fn($c) => ['desc' => $c->comp_desc, 'status' => $c->comp_status])) }}">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-cleaners-msg">No available cleaners found.</div>
                @endforelse
            </div>
        </div>

        <!-- Unavailable Tab Panel -->
        <div class="tab-panel" id="unavailable-pane">
            <div class="cleaner-list-container" aria-label="List of Unavailable Cleaners">
                @forelse($unavailableCleaners as $cleaner)
                    <div class="cleaner-card">
                        <!-- Profile Pic Container -->
                        <div class="profile-pic-container">
                            @if($cleaner->profile_pic)
                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}"
                                     alt="{{ $cleaner->cleaner_name }}'s Profile Picture"
                                     class="cleaner-profile-pic" loading="lazy">
                            @else
                                <div class="no-image-placeholder" aria-label="No Image Available">No Image</div>
                            @endif
                        </div>

                        <!-- Cleaner Info -->
                        <div class="cleaner-info">
                            <p class="cleaner-name">{{ $cleaner->cleaner_name }}</p>
                            <p class="cleaner-phone">Phone: {{ $cleaner->cleaner_phoneNo }}</p>
                            <p class="cleaner-building">Building: {{ $cleaner->building }}</p>
                            <p class="cleaner-status-label">
                                Status:
                                <span class="cleaner-status status-unavailable">Unavailable</span>
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions">
                            <button class="view-details-btn"
                                data-name="{{ $cleaner->cleaner_name }}"
                                data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : 'no_image' }}"
                                data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                                data-username="{{ $cleaner->cleaner_username }}"
                                data-building="{{ $cleaner->building }}"
                                data-status="{{ $cleaner->status }}"
                                data-complaints="{{ json_encode($cleaner->complaints->map(fn($c) => ['desc' => $c->comp_desc, 'status' => $c->comp_status])) }}">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-cleaners-msg">No unavailable cleaners found.</div>
                @endforelse
            </div>
        </div>
    </div><!-- /.tab-content -->
</div><!-- /.container -->

<!-- Cleaner Details Modal (unchanged) -->
<div id="cleanerModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-cleaner-name" aria-describedby="modal-cleaner-details">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close-button" aria-label="Close Modal">&times;</span>
            <div class="modal-body">
                <img src="" alt="Cleaner Profile Picture" class="modal-profile-pic" id="modal-profile-pic" loading="lazy" style="display: none;">
                <div id="modal-no-image-placeholder" class="modal-no-image-placeholder" aria-label="No Image Available" style="display: none;">No Image</div>
                <h2 class="modal-cleaner-name" id="modal-cleaner-name"></h2>
                <span class="modal-cleaner-username" id="modal-cleaner-username"></span>
                <p class="modal-cleaner-status" id="modal-cleaner-status"></p>
                <div class="modal-cleaner-details" id="modal-cleaner-details">
                    <p>
                        <strong>Phone Number:</strong>
                        <a href="#" class="modal-cleaner-phoneLink" target="_blank" rel="noopener noreferrer" id="modal-cleaner-phoneNo"></a>
                    </p>
                    <p>
                        <strong>Username:</strong>
                        <span id="modal-cleaner-username-detail"></span>
                    </p>
                    <p>
                        <strong>Building:</strong>
                        <span id="modal-cleaner-building"></span>
                    </p>
                </div>

                <div class="assigned-complaints">
                    <h3>Assigned Complaints:</h3>
                    <!-- Loading Spinner -->
                    <div id="complaint-spinner" style="display: none; margin-bottom: 1rem;" aria-label="Loading...">
                        <i class="fas fa-spinner fa-spin loader"></i> Loading...
                    </div>
                    <ul id="modal-cleaner-complaints" style="display: none;">
                        <!-- Assigned complaints will be injected here -->
                    </ul>
                    <p id="complaint-message" style="display: none; color: #555; font-size: 0.95rem;">
                        Cleaners still have ongoing tasks to be completed.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.cleanersIndexRoute = "{{ route('supervisor.cleaners') }}";

    document.addEventListener('DOMContentLoaded', () => {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels  = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Deactivate all tab buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));

                // Activate the clicked button
                button.classList.add('active');

                // Hide all tab panels
                tabPanels.forEach(panel => {
                    panel.classList.remove('active');
                });

                // Show the target panel
                const targetId = button.getAttribute('data-tab');
                const targetPanel = document.getElementById(targetId);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                }
            });
        });
    });
</script>

@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
    'resources/supervisor/cleaner.js', 
])
@endpush
