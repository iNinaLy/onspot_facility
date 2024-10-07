

<x-app-layout>
    <style>
        /* Additional custom styles */
        html {
            scroll-behavior: smooth; /* Enable smooth scrolling */
        }

        .button-transition {
            transition: all 0.3s ease-in-out;
            transform: scale(1);
        }
        .button-transition:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 10px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Fade in effect */
        .fade-in {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .fade-in.visible {
            opacity: 1;
        }
        
        body {
            background-color: #ffffff;
        }

        .task-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Make width consistent */
            height: 150px; /* Set a fixed height for consistency */
        }

        .task-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .task-icon {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr)); /* For mobile */
            gap: 20px;
        }

        @media (min-width: 640px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)); /* For medium screens */
            }
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)); /* For large screens */
            }
        }

        .stats-card {
        background-color: #f3f4f6; /* Light background */
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        transition: transform 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        height: 150px; /* Consistent height */
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        color: #374151;
    }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Complaints Management Card -->
            <div class="flex bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg mt-6 card-hover fade-in" id="complaints-card">
                <div class="p-6 w-1/2">
                    <h1 class="text-2xl font-bold">Received Complaints?</h1>
                    <h2 class="text-xl mt-2">Start Assigning Cleaners</h2>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        Lorem ipsum dolor sit amet. A illum impedit qui quia repudiandae vel dolorem
                        voluptate qui corrupti consequatur nam voluptatem iste?
                    </p>
                    <!-- Assign Tasks Button -->
                    <button onclick="window.location.href='/supervisor/complaints/index'" 
                            class="mt-4 bg-[#2e5675] text-white rounded-lg px-6 py-2 hover:bg-[#1d3e55] transition duration-200 button-transition">
                        Assign Tasks
                    </button>
                </div>
                <div class="w-2/4 flex items-center justify-center">
                    <img src="{{ asset('/images/vacuum_cleaner.png') }}" alt="Vacuum" class="w-3/4 h-auto transition duration-400 ease-in-out transform hover:scale-105 rounded-lg">
                </div>
            </div>
            <!-- End of Complaints Management Card -->

            <!-- Check Cleaners On Duty Section -->
            <div class="flex items-center bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg mt-6 card-hover fade-in" id="cleaners-card">
                <!-- Image Section -->
                <div class="w-1/2">
                    <img src="{{ asset('/images/cleaner.png') }}" alt="Cleaner Image" class="w-full h-auto transition duration-300 ease-in-out transform hover:scale-105">
                </div>

                <!-- Content Section -->
                <div class="w-1/2 p-6">
                    <h1 class="text-2xl font-bold">But Who’s Available?</h1>
                    <h2 class="text-xl mb-2">Check Cleaners On Duty</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Lorem ipsum dolor sit amet. A illum impedit qui quia repudiandae vel dolorem
                        voluptate qui corrupti consequatur nam voluptatem iste?
                    </p>
                    <!-- Search Cleaners Button -->
                    <button onclick="window.location.href='/supervisor/cleaners'" 
                            class="mt-4 bg-[#2e5675] text-white rounded-lg px-6 py-2 hover:bg-[#1d3e55] transition duration-200 button-transition">
                        Search Cleaners
                    </button>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                    <!-- Total Cleaners -->
                    <div class="stats-card">
                        <div class="text-lg font-semibold">Total Cleaners</div>
                        <div class="stats-number" data-count="{{ $totalCleaners }}">0</div>
                    </div>

                    <!-- Available Cleaners -->
                    <div class="stats-card">
                        <div class="text-lg font-semibold">Available Cleaners</div>
                        <div class="stats-number" data-count="{{ $availableCleaners }}">0</div>
                    </div>
                </div>
                </div>
            </div>
            <!-- End of Check Cleaners On Duty Section -->

          
        <!-- Add some margin between sections -->
        <div class="mt-10">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Recent Complaint</h2>
            <a href="{{ route('supervisor.complaints.index') }}" class="text-blue-500 hover:underline text-sm">See all</a>
        </div>
        <div class="w-full max-w-xl mx-auto mt-4">
            @if(isset($recentComplaint) && $recentComplaint)
                <div class="bg-blue-200 rounded-lg shadow-lg p-8 flex flex-col items-start">
                    <div class="flex justify-between items-center w-full">
                        <div class="flex items-center space-x-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-900">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 16.862A9.953 9.953 0 0122.5 12c0-5.523-4.477-10-10-10S2.5 6.477 2.5 12c0 2.61 1.007 4.98 2.638 6.862M15 12a3 3 0 11-6 0 3 3 0 016 0zm3 5.5a6.7 6.7 0 01-12 0" />
                            </svg>
                            <div>
                                <div class="text-lg text-blue-800 font-semibold">{{ $recentComplaint->comp_location }}</div>
                                <div class="text-xl text-blue-900 font-bold">{{ $recentComplaint->comp_desc }}</div>
                            </div>
                        </div>
                        <div class="text-lg text-gray-600">{{ $recentComplaint->comp_time }}</div>
                    </div>
                    <div class="flex items-center text-lg text-blue-900 mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 3h-15m15 0a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-15a1.5 1.5 0 01-1.5-1.5v-15A1.5 1.5 0 014.5 3m15 0V.75m-15 2.25V.75m13.5 9.75h-13.5m0 0a4.5 4.5 0 014.5-4.5h4.5a4.5 4.5 0 014.5 4.5m-13.5 0v9a4.5 4.5 0 004.5 4.5h4.5a4.5 4.5 0 004.5-4.5v-9" />
                        </svg>
                        <span class="font-medium">{{ $recentComplaint->comp_date }}</span>
                    </div>
                </div>
            @else
                <p class="text-gray-600 text-center mt-4">No recent complaints available.</p>
            @endif
        </div>
    </div>


        </div>
        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tasks Section -->
            <div class="mt-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Tasks</h2>
                <div class="grid">
                    <!-- Task Card 1 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-bucket-fill task-icon"></i>
                        <p class="mt-2 text-gray-800">Mopping</p>
                    </div>

                    <!-- Task Card 2 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-hand-thumbs-up-fill task-icon"></i>
                        <p class="mt-2 text-gray-800">Wiping</p>
                    </div>

                    <!-- Task Card 3 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-brush-fill task-icon"></i>
                        <p class="mt-2 text-gray-800">Sweeping</p>
                    </div>

                    <!-- Task Card 4 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-wind task-icon"></i>
                        <p class="mt-2 text-gray-800">Vacuuming</p>
                    </div>

                    <!-- Task Card 5 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-building task-icon"></i>
                        <p class="mt-2 text-gray-800">Organizing</p>
                    </div>

                    <!-- Task Card 6 -->
                    <div class="task-card fade-in">
                        <i class="bi bi-window-dock task-icon"></i>
                        <p class="mt-2 text-gray-800">Window Cleaning</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-footer />

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        // Intersection Observer for fade-in effects
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Stop observing once visible
                }
            });
        });

        // Observe all fade-in elements
        const fadeIns = document.querySelectorAll('.fade-in');
        fadeIns.forEach(element => {
            observer.observe(element);
        });


        document.querySelectorAll('.stats-number').forEach(element => {
        const countTo = parseInt(element.getAttribute('data-count'));
        let count = 0;
        const increment = Math.ceil(countTo / 100); // Adjust speed by changing this divisor

        const updateCount = () => {
            count += increment;
            if (count >= countTo) {
                element.innerText = countTo;
            } else {
                element.innerText = count;
                requestAnimationFrame(updateCount);
            }
        };

        updateCount();
    });
    </script>
</x-app-layout>
