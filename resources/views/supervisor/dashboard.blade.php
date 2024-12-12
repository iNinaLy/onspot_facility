@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@push('styles')
    <!-- External Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Compiled Supervisor Stylesheets -->
    <link href="{{ mix('css/supervisor/app.css') }}" rel="stylesheet" />
    <link href="{{ mix('css/supervisor/cleaner.css') }}" rel="stylesheet" />

   
@endpush

    @section('content')
    <div class="container py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Message at the Top -->
            <div class="mb-6 text-2xl font-semibold text-gray-800">
                Welcome, {{ Auth::user()->name }}!
            </div>

          <!-- Pending Complaints -->

        @if($pendingComplaints > 0)
            <div class="flex items-center p-6 bg-red-50 border-l-4 border-red-600 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 bg-red-100 rounded-full">
                        <i class="fa fa-exclamation-circle text-red-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <p class="text-lg font-bold text-red-800">
                        Pending Complaints
                    </p>
                    <p class="text-sm text-red-700 mt-1">
                        You have <strong>{{ $pendingComplaints }}</strong> pending complaint{{ $pendingComplaints > 1 ? 's' : '' }} that need your attention.
                    </p>
                </div>
                <div>
                    <a href="{{ route('supervisor.complaints.index') }}" 
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg shadow-md hover:bg-red-700 hover:shadow-lg transition duration-300">
                        View Complaints
                    </a>
                </div>
            </div>
        @else
            <div class="flex items-center p-6 bg-gray-50 border-l-4 border-gray-300 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 bg-gray-200 rounded-full">
                        <i class="fa fa-check-circle text-gray-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-lg font-bold text-gray-800">
                        No Pending Complaints
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        You’re all caught up! Great job staying on top of things.
                    </p>
                </div>
            </div>
        @endif





            <!-- Complaints Management Card (Assign Section - Borderless) -->
            <div class="flex overflow-hidden sm:rounded-lg mt-6" id="complaints-card">
                <div class="p-6 w-full md:w-1/2">
                    <h1 class="text-xl font-bold">Received Complaints?</h1>
                    <h2 class="text-lg mt-2">Start Assigning Cleaners</h2>
                    <p class="mt-3 text-gray-600">
                        Efficiently manage complaints and assign tasks to available cleaners. Ensure all complaints are addressed promptly.
                    </p>
                    <button onclick="window.location.href='/supervisor/complaints'" 
                            class="mt-4 button-transition">
                        Assign Tasks
                    </button>
                </div>
                <div class="w-full md:w-1/2 flex items-center justify-center">
                    <img src="{{ asset('/images/vacuum_cleaner.png') }}" alt="Vacuum" class="w-3/4 md:w-2/3 h-auto rounded-lg">
                </div>
            </div>

            <!-- Divider Between Assign and Cleaner Sections -->
            <hr class="section-divider">

            <!-- Check Cleaners On Duty Section (Borderless) -->
            <div class="flex items-center overflow-hidden sm:rounded-lg cleaners-card fade-in">
                <div class="w-full md:w-1/2">
                    <img src="{{ asset('/images/cleaner.png') }}" alt="Cleaner Image" class="w-full h-auto">
                </div>
                <div class="w-full md:w-1/2 p-6">
                    <h1 class="text-xl font-bold">But Who’s Available?</h1>
                    <h2 class="text-lg mb-2">Check Cleaners On Duty</h2>
                    <p class="text-gray-600 mb-4">
                        Find out which cleaners are currently on duty and available for assignments.
                    </p>
                    <button onclick="window.location.href='/supervisor/cleaners'" 
                            class="mt-4 button-transition">
                        Search Cleaners
                    </button>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="stats-card">
                            <div class="text-lg font-semibold">Total Cleaners</div>
                            <div class="stats-number">{{ $totalCleaners }}</div>
                        </div>
                        <div class="stats-card">
                            <div class="text-lg font-semibold">Available Cleaners</div>
                            <div class="stats-number">{{ $availableCleaners }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider Between Cleaner and Recent Complaint Sections -->
            <hr class="section-divider">

            <div class="mt-10">
                <div class="section-title">
                    <h2 class="ios-heading">Assigned Task</h2>
                    <a href="{{ route('supervisor.history') }}" class="ios-link">See all</a>
                </div>
                <div class="w-full max-w-lg mx-auto mt-4">
                    @if($recentOngoingComplaint)
                        <div class="ios-card">
                            <div class="ios-card-header">
                                <div class="ios-card-title">
                                    <strong>Location:</strong> {{ $recentOngoingComplaint->comp_location ?? 'N/A' }}
                                </div>
                                <small class="ios-card-date">
                                    {{ \Carbon\Carbon::parse($recentOngoingComplaint->comp_date)->format('d M Y') }}
                                </small>
                            </div>
                            <div class="ios-card-content">
                                <p><strong>Description:</strong> {{ $recentOngoingComplaint->comp_desc }}</p>
                            </div>
                            <div class="ios-card-status">
                                <p>
                                    <strong>Status:</strong>
                                    <span class="ios-badge ios-status-ongoing">
                                        Ongoing
                                    </span>
                                </p>
                            </div>
                            <button class="ios-button-small" onclick="toggleDetails({{ $recentOngoingComplaint->id }})">
                                View Details
                            </button>
                            <div id="details-{{ $recentOngoingComplaint->id }}" class="ios-details">
                                <h6 class="mt-3">Assigned Cleaners:</h6>
                                <ul class="ios-cleaners-list">
                                    @forelse ($recentOngoingComplaint->cleaners as $cleaner)
                                        <li class="ios-cleaner-item">
                                            <strong>{{ $cleaner->cleaner_name }}</strong>
                                            <div class="ios-phone-link">
                                                <i class="fas fa-phone-alt"></i> {{ $cleaner->cleaner_phoneNo ?? 'N/A' }}
                                            </div>
                                        </li>
                                    @empty
                                        <li class="ios-cleaner-item">No cleaners assigned.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @else
                        <p class="ios-empty-state">All task completed.</p>
                    @endif
                </div>
            </div>


            <!-- Tasks Section -->
            <div class="mt-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tasks</h2>
                <div class="task-wrapper">
                    <!-- Task Cards -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/mop.svg') }}" alt="Mopping Icon">
                        <div class="task-info">
                            <h1>Mopping</h1>
                            <p>Ensure floors are spotless by mopping regularly.</p>
                        </div>
                    </div>
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/wipe.svg') }}" alt="Wiping Icon">
                        <div class="task-info">
                            <h1>Wiping</h1>
                            <p>Wipe surfaces to remove dust and grime.</p>
                        </div>
                    </div>
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/toilet.svg') }}" alt="Toilet Cleaning Icon">
                        <div class="task-info">
                            <h1>Toilet Cleaning</h1>
                            <p>Maintain hygiene by cleaning restrooms thoroughly.</p>
                        </div>
                    </div>
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/vacuuming.svg') }}" alt="Vacuuming Icon">
                        <div class="task-info">
                            <h1>Vacuuming</h1>
                            <p>Keep carpets clean by regular vacuuming.</p>
                        </div>
                    </div>
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/desk.svg') }}" alt="Organizing Icon">
                        <div class="task-info">
                            <h1>Organizing</h1>
                            <p>Arrange items neatly to maintain order.</p>
                        </div>
                    </div>
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/window.svg') }}" alt="Window Cleaning Icon">
                        <div class="task-info">
                            <h1>Window Cleaning</h1>
                            <p>Clean windows for a clear view.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

@push('scripts')
@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
    'resources/supervisor/complaint.js',
])
@endpush