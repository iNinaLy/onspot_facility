<x-app-layout>
<head>
    <style>
        /* Global Styling */
        body, html {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        /* Main container styling */
        .container {
            max-width: 900px;
            margin: 80px auto; /* Ensure it starts below the navbar */
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* Back button styling */
        .back-button {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            text-decoration: none;
            color: #3498db;
            font-weight: 600;
        }

        .back-button svg {
            margin-right: 0.5rem;
            width: 18px;
            height: 18px;
            fill: #3498db;
        }

        /* Title and Status Styling */
        .title-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .title-status h1 {
            font-size: 1.8rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .status {
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .status.pending {
            background-color: #f1c40f;
            color: #fff;
        }

        .status.resolved {
            background-color: #2ecc71;
            color: #fff;
        }

        .status.ongoing {
            background-color: #3498db;
            color: #fff;
        }

        /* Image Section Styling */
        .image-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .image-section img {
            max-height: 250px;
            width: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        /* Details Section */
        .details-section {
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 3rem;
        }

        .details-section h2 {
            font-size: 1.4rem;
            color: #34495e;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .details-section p {
            color: #555;
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }

        /* Assign Cleaner Form Styling */
        .assign-cleaner {
            background-color: #f9fafb;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .assign-cleaner h3 {
            font-size: 1.5rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .assign-cleaner label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .assign-cleaner select, 
        .assign-cleaner input {
            width: 100%;
            padding: 0.6rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            margin-bottom: 1rem;
            transition: border-color 0.3s;
        }

        .assign-cleaner select:focus, 
        .assign-cleaner input:focus {
            border-color: #3498db;
            outline: none;
        }

        .cleaner-item {
            display: flex;
            align-items: center;
            padding: 0.6rem;
            background-color: #f4f7fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 0.75rem;
            transition: background-color 0.3s;
        }

        .cleaner-item:hover {
            background-color: #ecf0f1;
        }

        .cleaner-item label {
            font-size: 1rem;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .submit-button {
            background-color: #3498db;
            color: #fff;
            padding: 0.75rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-button:hover:enabled {
            background-color: #2c7cc1;
            transform: translateY(-2px);
        }

        .submit-button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
</head>

<div class="container">
    <!-- Breadcrumb -->
    <div class="back-button">
        <a href="{{ route('supervisor.complaints.index') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon">
                <path d="M12 2l-1.41 1.41L17.17 9H3v2h14.17l-6.58 6.59L12 22l-10-10z"/>
            </svg>
            Back to Complaints Index
        </a>
    </div>

    <!-- Title and Status -->
    <div class="title-status">
        <h1>Complaint Details</h1>
        <span class="status {{ strtolower($complaint->comp_status) }}">{{ ucfirst($complaint->comp_status) }}</span>
    </div>

    <!-- Image Section -->
    <div class="image-section">
        @if($complaint->images && $complaint->images->isNotEmpty())
            <img src="{{ asset('storage/' . $complaint->images->first()->path) }}" alt="Complaint Image">
        @else
            <p class="text-gray-500">No additional images</p>
        @endif
    </div>

    <!-- Complaint Information -->
    <div class="details-section">
        <h2>Office Cleaning</h2>
        <p><strong>Room:</strong> {{ $complaint->comp_location }}, Floor</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</p>
        <p><strong>Complaint by:</strong> {{ $complaint->officer->officer_name ?? 'Unknown Officer' }}</p>
        <h3>Task Description</h3>
        <textarea class="w-full border border-gray-300 rounded-lg h-24" readonly>{{ $complaint->comp_desc }}</textarea>
    </div>

    <!-- Assign Cleaner Section -->
    @if($complaint->comp_status === 'pending')
    <div class="assign-cleaner">
        <h3>Assign Cleaner</h3>
        <form action="{{ route('assign.cleaner', ['id' => $complaint->id]) }}" method="POST" id="assignCleanerForm">
            @csrf
            <input type="hidden" name="id" value="{{ $complaint->id }}">
            
            <label for="no_of_cleaners">Number of Cleaners</label>
            <select id="no_of_cleaners" name="no_of_cleaners" class="custom-select">
                <option value="">Select number of cleaners</option>
                @for($i = 1; $i <= 3; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            <div class="mt-4" id="cleaner-selection" style="display: none;">
                <label>Select Cleaners</label>
                <div id="cleaner-list"></div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="submit-button" disabled>Submit</button>
            </div>
        </form>
    </div>
    @endif
</div>

<!-- JavaScript -->
<script>
    const numberOfCleanersSelect = document.getElementById('no_of_cleaners');
    const cleanerSelectionDiv = document.getElementById('cleaner-selection');
    const cleanerList = document.getElementById('cleaner-list');
    const submitButton = document.querySelector('.submit-button');

    let maxCleaners = 0;

    numberOfCleanersSelect.addEventListener('change', function() {
        maxCleaners = parseInt(this.value);
        cleanerSelectionDiv.style.display = maxCleaners ? 'block' : 'none';
        fetchAvailableCleaners(maxCleaners);
        updateSelectedCleanersDisplay();
    });

    function fetchAvailableCleaners(count) {
        fetch(`/supervisor/api/cleaners?limit=${count}`)
            .then(response => response.json())
            .then(data => {
                cleanerList.innerHTML = '';
                data.cleaners.forEach(cleaner => {
                    const cleanerItem = document.createElement('div');
                    cleanerItem.classList.add('cleaner-item');

                    cleanerItem.innerHTML = `
                        <input type="checkbox" id="cleaner-${cleaner.id}" class="cleaner-checkbox" name="cleaners[]" value="${cleaner.id}">
                        <label for="cleaner-${cleaner.id}">
                            ${cleaner.cleaner_name} (${cleaner.cleaner_phoneNo})
                        </label>
                    `;
                    cleanerList.appendChild(cleanerItem);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Unable to fetch cleaners.');
            });
    }

    cleanerList.addEventListener('change', function(e) {
        const checkboxes = cleanerList.querySelectorAll('input[type="checkbox"]');
        const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;

        if (checkedCount > maxCleaners) {
            e.target.checked = false;
            alert('You can only select up to ' + maxCleaners + ' cleaners.');
        }

        updateSelectedCleanersDisplay();
    });

    function updateSelectedCleanersDisplay() {
        const checkboxes = cleanerList.querySelectorAll('input[type="checkbox"]');
        const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
        submitButton.disabled = checkedCount === 0;
    }
</script>
</x-app-layout>
