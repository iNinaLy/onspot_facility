<x-app-layout>
    <x-slot name="header"></x-slot>

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

        /* Button styling */
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

        /* Recent Complaint Card */
        .recent-complaint-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: transform 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .recent-complaint-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .recent-complaint-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 8px;
            background-color: #2e5675;
        }

        .recent-complaint-card .info {
            flex: 1;
        }

        .recent-complaint-card .info h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .recent-complaint-card .info h2 img {
            margin-right: 8px;
        }

        .recent-complaint-card .info p {
            color: #4b5563;
            margin-bottom: 8px;
        }

        .recent-complaint-card .timestamp {
            font-size: 0.9rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
        }

        .recent-complaint-card .timestamp img {
            margin-right: 4px;
        }

        /* Cleaners Card */
        .cleaners-card {
            background-color: #ffffff;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            transition: transform 0.3s ease-in-out;
        }

        .cleaners-card:hover {
            transform: translateY(-5px);
        }

        .cleaners-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        /* Complaints Card */
        #complaints-card {
            background-color: #ffffff;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            transition: transform 0.3s ease-in-out;
        }

        #complaints-card:hover {
            transform: translateY(-5px);
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

            <!-- New Complaints Notification -->
            @if(isset($newComplaints) && is_countable($newComplaints) && count($newComplaints) > 0)
                <div class="new-complaint-notification">
                    <img src="{{ asset('img/svg/notification.svg') }}" class="notification-icon" alt="Notification Icon">
                    <div class="notification-text">
                        <strong>New Complaints Available!</strong>
                        You have {{ count($newComplaints) }} new complaints.
                        <a href="{{ route('supervisor.complaints.index') }}">View all complaints</a>
                    </div>
                </div>
            @else
                <div class="new-complaint-notification">
                    <img src="{{ asset('img/svg/notification.svg') }}" class="notification-icon" alt="Notification Icon">
                    <div class="notification-text">
                        <strong>No New Complaints</strong>
                        There are currently no new complaints.
                    </div>
                </div>
            @endif

            <!-- Complaints Management Card -->
            <div class="flex bg-white overflow-hidden sm:rounded-lg mt-6" id="complaints-card">
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

            <!-- Check Cleaners On Duty Section -->
            <div class="flex items-center bg-white overflow-hidden sm:rounded-lg mt-6 cleaners-card fade-in">
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

            <!-- Recent Complaint Section -->
            <div class="mt-10">
                <div class="section-title">
                    <h2>Recent Complaint</h2>
                    <a href="{{ route('supervisor.complaints.index') }}">See all</a>
                </div>
                <div class="w-full max-w-lg mx-auto mt-4">
                    @if($recentComplaints->isNotEmpty())
                        <div class="recent-complaint-card">
                            <div class="info">
                                <h2>
                                    <img src="{{ asset('img/svg/location.svg') }}" class="inline w-5 h-5" alt="Location Icon">
                                    {{ $recentComplaints->first()->comp_location }}
                                </h2>
                                <p>{{ $recentComplaints->first()->comp_desc }}</p>
                                <p class="timestamp">
                                    <img src="{{ asset('img/svg/calendar.svg') }}" class="inline w-4 h-4" alt="Calendar Icon">
                                    {{ \Carbon\Carbon::parse($recentComplaints->first()->comp_date)->format('Y-m-d') }} at {{ $recentComplaints->first()->comp_time }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600 text-center">No recent complaints available.</p>
                    @endif
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="mt-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tasks</h2>
                <div class="task-wrapper">
                    <!-- Task Card 1 -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/mop.svg') }}" alt="Mopping Icon">
                        <div class="task-info">
                            <h1>Mopping</h1>
                            <p>Ensure floors are spotless by mopping regularly.</p>
                        </div>
                    </div>

                    <!-- Task Card 2 -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/wipe.svg') }}" alt="Wiping Icon">
                        <div class="task-info">
                            <h1>Wiping</h1>
                            <p>Wipe surfaces to remove dust and grime.</p>
                        </div>
                    </div>

                    <!-- Task Card 3 -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/toilet.svg') }}" alt="Toilet Cleaning Icon">
                        <div class="task-info">
                            <h1>Toilet Cleaning</h1>
                            <p>Maintain hygiene by cleaning restrooms thoroughly.</p>
                        </div>
                    </div>

                    <!-- Task Card 4 -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/vacuuming.svg') }}" alt="Vacuuming Icon">
                        <div class="task-info">
                            <h1>Vacuuming</h1>
                            <p>Keep carpets clean by regular vacuuming.</p>
                        </div>
                    </div>

                    <!-- Task Card 5 -->
                    <div class="task-card fade-in">
                        <img src="{{ asset('img/svg/desk.svg') }}" alt="Organizing Icon">
                        <div class="task-info">
                            <h1>Organizing</h1>
                            <p>Arrange items neatly to maintain order.</p>
                        </div>
                    </div>

                    <!-- Task Card 6 -->
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
</x-app-layout>
