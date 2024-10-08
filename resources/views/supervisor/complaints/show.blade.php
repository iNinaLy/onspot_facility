    <x-app-layout>
        <style>
           /* Global Styling */
            body, html {
                font-family: Arial, sans-serif;
                color: #333;
            }

            /* General Page Styling */
            .font-bold1 {
                font-weight: 700;
                margin-top: 5rem;
                margin-left: 8rem;
            }

            .text-gray1-700 {
                color: rgb(3 4 4);
                margin-left: 8rem;
            }

            .bg-gray-100 {
                background-color: rgb(217, 219, 222);
                padding: 6rem 5rem;
            }

            /* Grid Layout */
            .grid-cols-1 {
                margin-left: 8rem;
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                gap: 2rem;
                justify-items: stretch;
                justify-content: space-between;
                align-content: space-evenly;
                align-items: start;
            }

            .lg\:grid-cols-3 {
                grid-template-columns: 1fr 2fr;
                gap: 2rem;
            }

            .assign-cleaner .grid-cols-1 {
                    grid-template-columns: 1fr 1fr;
                    display: grid;
                    gap: 2rem;
                    margin-left: 0.2rem;
                    align-items: baseline;
                    align-content: center;
            }

            /* Image Section */
            .image-section {
                background-color: rgb(245, 245, 245);
                border-radius: 10px;
                padding: 10rem;
                height: auto;
                text-align: center;
                margin: 2rem auto;
            }

            .additional-images {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
            }

            /* Details Section */
            .details-section {
                background-color: white;
                padding: 2rem;
                border-radius: 10px;
                margin: 0 8rem 0 4rem;
            }

            .details-section h2,
            .details-section h3 {
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 1rem;
            }

            .details-section p {
                color: gray;
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            /* Task Section */
            .task-section {
                display: flex;
                justify-content: space-between;
            }

            /* Assign Cleaner Section */
            .assign-cleaner {
                background-color: #ffffff;
                padding: 2rem;
                box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
                margin: 2rem auto;
                border-radius: 8px;
                width: 80%;
            }

            .assign-cleaner h3 {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
            }

            .assign-cleaner label {
                font-weight: 500;
                margin-bottom: 0.5rem;
                display: block;
            }

            .assign-cleaner select,
            .assign-cleaner input {
                width: 10%;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                transition: border-color 0.3s;
                margin-right: 1rem;
            }
                        /* Dropdown Cleaner Styling */
            .assign-cleaner .dropdown-cleaner {
                display: none;
                position: absolute;
                background-color: white;
                border: 1px solid #ddd;
                max-height: 200px;
                overflow-y: auto;
                z-index: 10;
                border-radius: 4px;
                width: 100%;
                box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
                margin-top: 0.25rem;
            }

            .assign-cleaner .dropdown-cleaner.active {
                display: block;
            }

            .assign-cleaner .dropdown-cleaner div {
                padding: 0.75rem;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .assign-cleaner .dropdown-cleaner div:hover {
                background-color: #f3f4f6;
            }

            /* Submit Button */
            .assign-cleaner .submit-button {
                background-color: #2563eb;
                color: white;
                padding: 0.75rem 2rem;
                border: none;
                border-radius: 0.375rem;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .assign-cleaner .submit-button:hover:enabled {
                background-color: #1e40af; /* Darker blue */
            }

            .assign-cleaner .submit-button:disabled {
                background-color: #9ca3af; /* Light gray */
                cursor: not-allowed;
            }

            /* Responsive Layout */
            @media (min-width: 640px) {
                .assign-cleaner .grid-cols-1 {
                    grid-template-columns: 1fr 1fr;
                    display: grid;
                    gap: 2rem;
                }
            }

            .cleaner-item {
                display: flex;
                align-items: center;
                margin-bottom: 0.5rem;
            }

            .cleaner-checkbox {
                width: 15px; /* Smaller checkbox */
                height: 15px; /* Smaller checkbox */
            }

        </style>

        <div class="container mx-auto px-6 py-8">
            <!-- Breadcrumb -->
            <div class="text-sm text-gray-500 mb-6">
                <a href="{{ route('supervisor.complaints.index') }}" class="text-blue-500">Home</a> / Complaints / Details
            </div>

            <!-- Complaint Status -->
            <div class="mb-4">
                <h1 class="text-2xl font-bold1">Complaint Details</h1>
                <p class="text-lg text-gray1-700">Status: 
                    <span class="{{ $complaint->comp_status === 'pending' ? 'text-yellow-500' : ($complaint->comp_status === 'resolved' ? 'text-green-500' : 'text-red-500') }}">
                        {{ ucfirst($complaint->comp_status) }}
                    </span>
                </p>
            </div>

            <!-- Main Layout: Left Image Section and Right Details Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Left Section: Image and Details -->
                <div class="space-y-8">
                    <!-- Image Section (Main Image) -->
                    <div class="image-section">
                        @if($complaint->images && $complaint->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $complaint->images->first()->path) }}" alt="Complaint Image" class="object-cover h-full w-full rounded-lg">
                        @else
                            <p class="text-gray-500">No additional images</p>
                        @endif
                    </div>

                    <!-- Additional Images -->
                    <div class="additional-images">
                        @if($complaint->images && $complaint->images->isNotEmpty())
                            @foreach($complaint->images as $image)
                                @if(!$loop->first)
                                    <div class="bg-gray-100 h-24 rounded-lg shadow-md flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Complaint Image" class="object-cover h-full w-full rounded-lg">
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-gray-500 col-span-3 text-center"></p>
                        @endif
                    </div>
                </div>

                <!-- Right Section: Task Description and Task Included -->
                <div class="col-span-2 space-y-8">
                    <!-- Complaint Details Section -->
                    <div class="details-section">
                        <h2>Office Cleaning</h2>
                        <p>Room: {{ $complaint->comp_location }}, Floor</p>
                        <p>Date: {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</p>
                        <p>Complaint by: {{ $complaint->officer->officer_name ?? 'Unknown Officer' }}</p>
                    </div>

                    <!-- Task Description Section -->
                    <div class="details-section">
                        <h3>Task description</h3>
                        <textarea class="w-full p-3 border border-gray-300 rounded-lg h-24" readonly>{{ $complaint->comp_desc }}</textarea>
                    </div>

                    <!-- Task Included Section -->
                    <div class="details-section">
                        <h3>Task Included</h3>
                        <div class="task-section">
                            @if($complaint->tasks && $complaint->tasks->isNotEmpty())
                                @foreach($complaint->tasks as $task)
                                    <div class="flex flex-col items-center space-y-2">
                                        <img src="{{ asset($task->icon) }}" alt="{{ $task->name }}" class="h-8 w-8">
                                        <span class="text-xs text-gray-600">{{ $task->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500">No tasks included</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        <!-- Assign Cleaner Form -->
<!-- Assign Cleaner Form -->
<div class="assign-cleaner">
    <h3 class="font-bold text-lg">Assign Cleaner</h3>
    <form action="{{ route('assign.cleaner', $complaint->comp_id) }}" method="POST">
        @csrf
        <input type="hidden" name="comp_id" value="{{ $complaint->comp_id }}"> <!-- Store complaint ID -->

        <div>
            <!-- Number of Cleaners Selection -->
            <label for="no_of_cleaners">Number of Cleaners</label>
            <select id="no_of_cleaners" name="no_of_cleaners" class="custom-select">
                <option value="">Select number of cleaners</option>
                @for($i = 1; $i <= 3; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Cleaner Selection -->
        <div class="mt-4" id="cleaner-selection" style="display: none;">
            <label>Select Cleaners</label>
            <div id="cleaner-list">
                @foreach($cleaners as $cleaner)
                    @if($cleaner->status === 'available')
                        <div class="cleaner-item">
                            <input type="checkbox" id="cleaner-{{ $cleaner->id }}" class="cleaner-checkbox" value="{{ $cleaner->id }}">
                            <label for="cleaner-{{ $cleaner->id }}">{{ $cleaner->cleaner_name }}</label>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-right mt-4">
            <button type="submit" class="submit-button" disabled>Submit</button>
        </div>
    </form>
</div>

<!-- JavaScript to handle cleaner selection -->
<script>
    const numberOfCleanersSelect = document.getElementById('no_of_cleaners');
    const cleanerSelectionDiv = document.getElementById('cleaner-selection');
    const cleanerList = document.getElementById('cleaner-list');
    const submitButton = document.querySelector('.submit-button');

    let maxCleaners = 0;

    // Handle number of cleaners selection
    numberOfCleanersSelect.addEventListener('change', function() {
        maxCleaners = parseInt(this.value);
        cleanerSelectionDiv.style.display = maxCleaners ? 'block' : 'none'; // Show/Hide cleaner selection
        updateSelectedCleanersDisplay();
    });

    // Handle checkbox selection
    cleanerList.addEventListener('change', function(e) {
        const checkboxes = cleanerList.querySelectorAll('input[type="checkbox"]');
        const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;

        if (checkedCount > maxCleaners) {
            e.target.checked = false; // Uncheck if limit exceeded
            alert('You can only select up to ' + maxCleaners + ' cleaners.');
        }

        updateSelectedCleanersDisplay();
    });

    function updateSelectedCleanersDisplay() {
        const checkboxes = cleanerList.querySelectorAll('input[type="checkbox"]');
        const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
        submitButton.disabled = checkedCount === 0; // Enable submit button if at least one cleaner is selected
    }
</script>



    </div>
</x-app-layout>