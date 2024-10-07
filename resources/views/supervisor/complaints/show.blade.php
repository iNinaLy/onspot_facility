<x-app-layout>
    <style>

        .font-bold1 {
            font-weight: 700;
            margin-top: 5rem;
            margin-left: 8rem;
        }

        .text-gray1-700 {
            --tw-text-opacity: 1;
            color: rgb(3 4 4);
            margin-left: 8rem;
        }
                /* General Page Styling */
        .bg-gray-100 {
            background-color: rgb(217 219 222);
            padding: 6rem 5rem;
        }

        /* Grid Layout */
        .grid-cols-1 {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 2rem;
        }

        .lg\:grid-cols-3 {
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
           
        }
        /* Image Section */
        .image-section {
            background-color: rgb(245, 245, 245);
            border-radius: 10px;
            padding: 10rem;
            height: auto;
            text-align: center;
            margin-left: 8rem;
            margin-right: auto;
            margin-top: 2rem;
        }
        .additional-images {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        /* Task and Complaint Details */
        .details-section {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-right: 8rem;
            margin-left: 4rem;
        }

        .details-section h2, .details-section h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .details-section p {
            color: gray;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .task-section {
            display: flex;
            justify-content: space-between;
        }

        /* Assign Cleaner Section */
        .assign-cleaner {
            background-color: white;
            padding: 1rem;
            /* border-radius: 10px; */
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            /* margin-top: 2rem; */
            margin-bottom: 5rem;
            margin-left: 8rem;
            margin-right: 8rem;
        }

        .assign-cleaner form {
            display: flex;
            justify-content: space-between;
        }

        .assign-cleaner select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .custom-select {
            appearance: none;
            background-color: #f9fafb; /* Light gray background */
            border: 1px solid #d1d5db; /* Gray border */
            border-radius: 0.375rem; /* Rounded corners */
            padding: 0.5rem 1rem; /* Padding */
            font-size: 1rem; /* Font size */
            transition: border-color 0.3s; /* Smooth transition */
            cursor: pointer; /* Pointer cursor */
            position: relative; /* Position relative for the arrow */
        }

        .custom-select:hover {
            border-color: #2563eb; /* Blue border on hover */
        }

        .custom-select:focus {
            outline: none; /* Remove outline */
            border-color: #3b82f6; /* Darker blue border on focus */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* Shadow on focus */
        }

        .custom-select option {
            background-color: #ffffff; /* White background for options */
            color: #374151; /* Dark text color */
        }

        .submit-button {
            background-color: #2E5675;
            color: white;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 4rem;
            margin-top: 10rem;
        }
        .submit-button:hover:enabled {
            background-color: #2563eb; /* Darker blue on hover */
        }

        .submit-button:disabled {
            background-color: #d1d5db; /* Gray background when disabled */
            cursor: not-allowed; /* Not-allowed cursor */
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
            <p class="text-lg text-gray1-700">Status: <span class="{{ $complaint->comp_status === 'pending' ? 'text-yellow-500' : ($complaint->comp_status === 'resolved' ? 'text-green-500' : 'text-red-500') }}">{{ ucfirst($complaint->comp_status) }}</span></p>
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

        <!-- Assign Cleaner Section -->
        <div class="assign-cleaner">
            @if($complaint->comp_status === 'pending')
                <h3 class="font-bold text-lg mb-4">Assign Cleaner</h3>
                <form action="{{ route('assign.cleaner', $complaint->comp_id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Number of Cleaners -->
                        <div>
                            <label for="number_of_cleaners" class="block text-sm font-medium text-gray-700">Number of Cleaners</label>
                            <select id="number_of_cleaners" name="number_of_cleaners" class="border border-gray-300 rounded-lg p-2">
                                <option value="">Select number of cleaners</option>
                                @for($i = 1; $i <= 3; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Cleaners Selection -->
                        <div id="cleaner-selection" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700">Select Cleaner</label>
                            <div class="mt-2">
                                @foreach($cleaners as $cleaner)
                                    @if($cleaner->status === 'available')
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 rounded-full {{ $cleaner->status === 'available' ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                                            <input type="checkbox" id="cleaner-{{ $cleaner->id }}" name="cleaner[]" value="{{ $cleaner->id }}" class="mr-2 cleaner-checkbox">
                                            <label for="cleaner-{{ $cleaner->id }}" class="text-gray-700">{{ $cleaner->cleaner_name }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500">You can select up to <span id="max-cleaners">0</span> cleaners.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right mt-6">
                        <button type="submit" class="submit-button" disabled>Submit</button>
                    </div>
                </form>
            @else
                <!-- Show Assigned Cleaners -->
                <h3 class="font-bold text-lg mb-4">Assigned Cleaners</h3>
                @if($complaint->cleaners && count($complaint->cleaners) > 0)
                    <ul class="list-disc ml-5">
                        @foreach($complaint->cleaners as $cleaner)
                            <li>{{ $cleaner->cleaner_name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No cleaners assigned yet.</p>
                @endif
            @endif
        </div>

        <script>
            // JavaScript to handle dynamic selection of cleaners
            document.getElementById('number_of_cleaners').addEventListener('change', function() {
                const numberOfCleaners = parseInt(this.value);
                const cleanerSelectionDiv = document.getElementById('cleaner-selection');
                const checkboxes = document.querySelectorAll('.cleaner-checkbox');
                const maxCleanersText = document.getElementById('max-cleaners');
                const submitButton = document.querySelector('.submit-button');

                if (numberOfCleaners) {
                    cleanerSelectionDiv.style.display = 'block';
                    maxCleanersText.textContent = numberOfCleaners;

                    // Reset checkbox states
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = false;
                    });

                    // Limit selection based on number chosen
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

                            // Enable/Disable checkboxes based on selection count
                            checkboxes.forEach(cb => {
                                if (checkedCount >= numberOfCleaners && !cb.checked) {
                                    cb.disabled = true; // Disable unchecked checkboxes
                                } else {
                                    cb.disabled = false; // Enable checkboxes if limit not reached
                                }
                            });
                        });
                    });

                    submitButton.disabled = false; // Enable submit button
                } else {
                    cleanerSelectionDiv.style.display = 'none';
                    submitButton.disabled = true; // Disable submit button
                }
            });
        </script>
    </div>
</x-app-layout>
