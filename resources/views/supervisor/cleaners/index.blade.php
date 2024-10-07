<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth; /* Enable smooth scrolling */
        }
        .heading {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 10px;
            margin-top: 5rem;
            text-align: left;
            color: #374151;
        }

        /* Container styles */
        .container {
            max-width: 1200px; /* Set a maximum width for the container */
            margin: auto; /* Center the container */
            padding: 20px;
        }

        /* Search Bar Styles */
        .search-bar {
            display: flex;
            justify-content: flex-end; /* Align to the right */
            margin-bottom: 20px;
            position: relative; /* Required for absolute positioning of suggestions */
            width: 100%; /* Ensure it takes the full width */
        }

        .search-input {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 10px 15px;
            width: 300px; /* Adjusted width */
            outline: none;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: #5c6bc0; /* Change border color on focus */
        }

        .search-icon-container {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #e5e7eb;
            border-radius: 0.375rem; /* Match input's border radius */
            margin-left: 4px; /* Overlap the input slightly */
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-icon-container:hover {
            background-color: #cfd8dc; /* Change background on hover */
        }

        /* Suggestions Styles */
        .suggestions {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #ffffff;
            position: absolute; 
            width: 300px; 
            z-index: 1000; 
            max-height: 200px; 
            overflow-y: auto; 
            display: none; 
        }

        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #e5e7eb; 
        }

        .no-results {
            padding: 10px;
            color: #999; /* Light gray for no results */
            text-align: center;
        }

        /* Cleaner Cards Styles */
        .cleaner-card {
            width: 180px; /* Adjusted width */
            height: 180px; /* Adjusted height */
            background-color: #ffffff; /* White background for cards */
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
            text-align: center; /* Center text in the card */
        }

        .cleaner-card:hover {
            transform: translateY(-3px); /* Lift effect on hover */
        }

        .cleaner-icon {
            font-size: 3rem; /* Increase icon size */
            margin-bottom: 10px;
        }

        .cleaner-name {
            font-size: 1rem; /* Larger font size for cleaner names */
            font-weight: 500;
            color: #333; /* Darker text color */
        }

        .cleaner-status {
            font-size: 0.9rem; /* Smaller font size for status */
            font-weight: bold; /* Bold text */
            display: flex; /* Flexbox for status indicator */
            align-items: center; /* Center items vertically */
            margin-top: 5px; /* Spacing above status */
        }

        /* Status Colors */
        .status-indicator {
            width: 10px; /* Width of the status indicator */
            height: 10px; /* Height of the status indicator */
            border-radius: 50%; /* Make it circular */
            margin-right: 5px; /* Space between indicator and status text */
        }

        .status-available .status-indicator {
            background-color: #4CAF50; /* Green for available */
        }

        .status-unavailable .status-indicator {
            background-color: #f44336; /* Red for unavailable */
        }

        /* Grid Styles */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); /* Responsive grid */
            gap: 20px; /* Increased gap for cleaner spacing */
            justify-items: center; /* Center items in grid */
        }

        /* Total Cleaners Text Styles */
        .total-cleaners-text {
            font-size: 1.2rem; /* Adjusted font size */
            font-weight: 700;
            color: #374151; /* Dark gray */
            margin-bottom: 20px; /* Spacing below text */
            text-align: left; /* Align text to the left */
        }

        /* Heading Styles */
        .heading {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px; /* Adjusted spacing below heading */
            text-align: left; /* Align heading to the left */
            color: #374151; /* Dark gray */
        }
    </style>

    <div class="container py-12">
        <div class="flex flex-wrap justify-center">
            <!-- Left Section: Cleaner Cards -->
            <div class="flex-grow mb-6 md:mb-0 w-full md:w-3/4">


                <!-- Search Bar -->
                <h2 class="heading">Cleaners</h2>
                <div class="search-bar">
                    <input 
                        type="search" 
                        class="search-input" 
                        placeholder="Search for cleaners..." 
                        aria-label="Search" 
                        aria-describedby="search-addon" 
                        id="search-input" 
                    />
                    <span class="search-icon-container">
                        <i class="fas fa-search text-gray-600"></i>
                    </span>
                    <div id="suggestions" class="suggestions"></div> <!-- Suggestions container -->
                </div>

                <!-- Total Cleaners Text -->
                <div class="total-cleaners-text">
                    Total Cleaners: {{ $totalCleaners }} 
                    (Available: <span class="status-available"><span class="status-indicator"></span>{{ $availableCount}}</span>, 
                    Unavailable: <span class="status-unavailable"><span class="status-indicator"></span>{{ $unavailableCount }}</span>)
                </div>

                <!-- Cleaner Cards Grid -->
                <div class="grid">
                    @foreach($cleaners as $cleaner)
                        <div class="cleaner-card">
                            <div class="cleaner-icon">👤</div>
                            <p class="cleaner-name">{{ $cleaner->cleaner_name }}</p>
                            <p class="cleaner-status {{ $cleaner->status == 'available' ? 'status-available' : 'status-unavailable' }}">
                                <span class="status-indicator"></span>
                                {{ $cleaner->status }}
                            </p> 
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fetch cleaner names and statuses from backend
            const cleaners = @json($cleaners->map(function($cleaner) {
                return ['name' => $cleaner->cleaner_name, 'status' => $cleaner->status];
            }));

            const searchInput = document.getElementById('search-input');
            const suggestionsContainer = document.getElementById('suggestions');

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase(); // Get the input value, trimmed and in lower case
                suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                if (query) {
                    // Filter the cleaners to find matches
                    const filteredCleaners = cleaners.filter(cleaner => cleaner.name.toLowerCase().startsWith(query));

                    if (filteredCleaners.length > 0) {
                        filteredCleaners.forEach(cleaner => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.textContent = `${cleaner.name} - ${cleaner.status}`; // Display name and status
                            suggestionItem.className = 'suggestion-item';

                            suggestionItem.addEventListener('click', function() {
                                searchInput.value = cleaner.name; 
                                suggestionsContainer.innerHTML = ''; 
                                suggestionsContainer.style.display = 'none'; // Hide suggestions after selection
                            });

                            suggestionsContainer.appendChild(suggestionItem);
                        });
                        suggestionsContainer.style.display = 'block'; // Show suggestions
                    } else {
                        // Show "No cleaners found" message
                        const noResultsItem = document.createElement('div');
                        noResultsItem.textContent = 'No cleaners found';
                        noResultsItem.className = 'no-results'; // Add class for styling
                        suggestionsContainer.appendChild(noResultsItem);
                        suggestionsContainer.style.display = 'block'; // Show suggestions
                    }
                } else {
                    suggestionsContainer.style.display = 'none'; // Hide suggestions if input is empty
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                    suggestionsContainer.style.display = 'none'; // Hide suggestions when clicking outside
                }
            });
        });
    </script>
</x-app-layout>

