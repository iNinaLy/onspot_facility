<x-app-layout>
    <!-- Include custom fonts -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap">

    <style>
        /* Base styling */
        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }

        @media (prefers-color-scheme: dark) {
            .dark\:text-gray-200 {
                --tw-text-opacity: 1;
                color: rgb(0 0 0);
            }
        }

        /* Button styling with gradient and hover effect */
        .button-transition {
            transition: all 0.3s ease-in-out;
            transform: scale(1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background-color: #2e5675;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
        }

        .button-transition:hover {
            transform: scale(1.05);
            background-color: #24445c;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .content-wrapper {
            padding-top: 70px;
        }

        /* Fade-in effect */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Task card styling */
        .task-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
            justify-items: center;
        }

        .task-card {
            width: 200px;
            height: 240px;
            border-radius: 15px;
            padding: 1rem;
            background: white;
            position: relative;
            display: flex;
            align-items: flex-end;
            transition: 0.3s ease-out;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }

        .task-card:hover::before {
            opacity: 1;
        }

        .task-card:hover .task-info {
            opacity: 1;
            transform: translateY(0px);
        }

        .task-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2;
            transition: 0.5s;
            opacity: 0;
        }

        .task-card img {
            width: 60%;
            height: 60%;
            object-fit: contain;
            position: absolute;
            top: 20%;
            left: 20%;
        }

        .task-info {
            position: relative;
            z-index: 3;
            color: white;
            opacity: 0;
            transform: translateY(20px);
            transition: 0.5s;
            text-align: center;
            padding: 0 5px;
        }

        .task-info h1 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            font-family: "Inter", sans-serif;
        }

        .task-info p {
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            margin-top: 5px;
            font-family: "Inter", sans-serif;
        }

        /* New Complaints Notification */
        .new-complaint-notification {
            background-color: #e8f0f7;
            border-left: 4px solid #2e5675;
            padding: 15px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 20px;
            color: #2e5675;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .notification-icon {
            width: 24px;
            height: 24px;
            color: #2e5675;
        }

        .notification-text {
            font-size: 1rem;
            flex: 1;
        }

        .notification-text a {
            color: #2e5675;
            text-decoration: underline;
            font-weight: 600;
        }

        /* Container spacing */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .container {
                padding: 2rem;
            }
        }

        /* Section Titles */
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .section-title a {
            font-size: 1rem;
            color: #2e5675;
            text-decoration: underline;
            font-weight: 600;
        }

        /* Stats card */
        .stats-card {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease-in-out;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2e5675;
            margin-top: 8px;
        }

        /* Complaints Management and Cleaners Cards - Borderless */
        #complaints-card, .cleaners-card {
            background: none;
            box-shadow: none;
        }

        /* Divider Styling */
        .section-divider {
            border: none;
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
            margin: 40px 0;
        }

        /* Recent Complaint Card with hover effect */
       
        .ios-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ios-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .ios-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .ios-card-title {
            font-size: 16px;
            font-weight: 500;
            color: #1c1c1e;
        }

        .ios-card-date {
            font-size: 14px;
            color: #8e8e93;
        }

        .ios-card-content {
            font-size: 14px;
            color: #3a3a3c;
            margin-bottom: 1rem;
        }

        .ios-card-status {
            font-size: 14px;
            margin-bottom: 1rem;
        }

        .ios-badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 12px;
            text-transform: capitalize;
            background-color: #dff6ff;
            color: #007aff;
            font-weight: 600;
        }

        .ios-button-small {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e0e0e0;
            color: #333333;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
            margin-top: 10px;
        }

        .ios-button-small:hover {
            background-color: #cccccc;
        }

        .ios-details {
            margin-top: 1rem;
            background-color: #f2f2f7;
            border-radius: 12px;
            padding: 15px;
            display: none;
        }

        .ios-cleaners-list {
            list-style: none;
            padding: 0;
            margin-top: 10px;
        }

        .ios-cleaner-item {
            font-size: 14px;
            color: #1c1c1e;
            margin-bottom: 10px;
        }

        .ios-phone-link {
            font-size: 12px;
            color: #007aff;
        }

        .ios-phone-link i {
            margin-right: 5px;
        }

        .ios-empty-state {
            font-size: 14px;
            color: #8e8e93;
            text-align: center;
            margin-top: 20px;
        }

        /* Media Queries for Task Cards */
        @media (max-width: 768px) {
            .task-wrapper {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .task-wrapper {
                grid-template-columns: 1fr;
            }
        }


    </style>

    <div class="container py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Message at the Top -->
            <div class="mb-6 text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Welcome, {{ Auth::user()->name }}!
            </div>

     
            <!-- New Complaints Notification -->
            @if(isset($newComplaints) && is_countable($newComplaints) && count($newComplaints) > 0)
                <div class="flex items-center p-4 bg-blue-100 border-l-4 border-blue-500 rounded-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('img/svg/notification.svg') }}" class="h-8 w-8" alt="Notification Icon">
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-blue-900">
                            New Complaints Available!
                        </p>
                        <p class="text-sm text-blue-700">
                            You have {{ count($newComplaints) }} new complaints.
                        </p>
                        <a href="{{ route('supervisor.complaints.index') }}" class="text-sm font-medium text-blue-600 hover:underline">
                            View all complaints
                        </a>
                    </div>
                </div>
            @else
                <div class="flex items-center p-4 bg-gray-100 border-l-4 border-gray-400 rounded-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('img/svg/notification.svg') }}" class="h-8 w-8" alt="Notification Icon">
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">
                            No New Complaints
                        </p>
                        <p class="text-sm text-gray-700">
                            There are currently no new complaints.
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

    <x-footer />

    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include custom scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        });
        document.querySelectorAll('.fade-in').forEach(element => {
            observer.observe(element);
        });
    </script>

    <script>
        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            details.style.display = details.style.display === 'none' || details.style.display === '' ? 'block' : 'none';
        }
    </script>

</x-app-layout>
