@extends('layouts.app')

@section('title', 'Complaint Details')

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Add any custom styles here */
    .complaint-layout {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .image-section img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .details-section {
        flex: 1;
    }
    .details-section textarea {
        width: 100%;
        resize: none;
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        font-family: inherit;
    }
    .complaint-by {
        display: flex;
        align-items: center;
        margin-top: 15px;
    }
    .complaint-by img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .assign-section, .assigned-cleaners-section {
        margin-top: 30px;
    }
    .assigned-cleaners-section h3 {
        margin-bottom: 20px;
    }
    .assigned-cleaners-list {
        list-style: none;
        padding: 0;
    }
    .assigned-cleaners-list li {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .assigned-cleaners-list img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
    }
    .assigned-cleaners-details {
        display: flex;
        flex-direction: column;
    }
    .badge-pending {
        background-color: #ffc107;
        color: #fff;
        padding: 5px 10px;
        border-radius: 12px;
    }
    .badge-ongoing {
        background-color: #17a2b8;
        color: #fff;
        padding: 5px 10px;
        border-radius: 12px;
    }
    .badge-completed {
        background-color: #28a745;
        color: #fff;
        padding: 5px 10px;
        border-radius: 12px;
    }
    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
    }

    .modal-header, .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h4 {
        margin: 0;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }

    .cleaner-list {
        list-style: none;
        padding: 0;
    }

    .cleaner-item {
        margin-bottom: 10px;
    }

    .cleaner-item label {
        display: flex;
        align-items: center;
    }

    .cleaner-item img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        margin-right: 10px;
    }

    .selected-cleaner-name {
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<div class="navbar-spacer"></div>
<div class="container">
    <!-- Header -->
    <div class="header-container">
        <a href="{{ route('supervisor.complaints.index') }}" class="back-button">
            <img src="{{ asset('img/svg/back-arrow.svg') }}" alt="Back">
            Complaint / Details
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Complaint Layout -->
    <div class="complaint-layout">
        <!-- Image Section -->
        <div class="image-section">
            @if($complaint->getFirstMediaUrl('complaint_images'))
            <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" alt="Complaint Image">
            @else
            <span>No Image Available</span>
            @endif
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <h2>{{ $complaint->comp_desc }}</h2>
            <p class="meta"><strong>Location:</strong> {{ $complaint->comp_location }}</p>
            <p class="meta"><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</p>
            <p class="meta"><strong>Status:</strong> 
                <span class="badge 
                    @if($complaint->comp_status == 'pending') 
                        status-pending 
                    @elseif($complaint->comp_status == 'ongoing') 
                        status-ongoing 
                    @else 
                        status-completed 
                    @endif">
                    {{ ucfirst($complaint->comp_status) }}
                </span>
            </p>
            <textarea rows="4" readonly>{{ $complaint->comp_desc }}</textarea>

            <!-- Complaint By Section -->
            <div class="complaint-by">
                <img src="{{ asset('img/profile_pic') }}" alt="User Profile">
                <span>{{ $complaint->user->name ?? 'Unknown User' }}</span>
            </div>
        </div>
    </div>

    <!-- Assign or Assigned Cleaners Section -->
    @if (Auth::user()->role == 'supervisor')
        @if($complaint->comp_status == 'pending')
            <!-- Assign Cleaners Form -->
            <div class="assign-section">
                <h3>Assign Cleaners for this complaint.</h3>
                @if($availableCleaners->isEmpty())
                <div class="alert alert-warning">
                    Oops! No cleaners are currently available. Please try again later.
                </div>
                @else
                <p>Available cleaners: <strong>{{ $availableCleaners->count() }}</strong></p>
                <form action="{{ route('supervisor.complaints.assign-cleaner', $complaint->id) }}" method="POST">
                    @csrf
                    <div class="assign-form mb-3">
                        <label for="no_of_cleaners" class="form-label">Number of Cleaners:</label>
                        <select name="no_of_cleaners" id="no_of_cleaners" class="form-select">
                            @for ($i = 1; $i <= min($availableCleaners->count(), 3); $i++)
                            <option value="{{ $i }}" {{ old('no_of_cleaners') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" id="selectCleanersBtn" class="btn btn-primary">Select Cleaners</button>
                    <div id="selectedCleaners" class="mt-3"></div>
                    <button type="submit" class="btn btn-success mt-2 d-none" id="assignBtn">Assign</button>
                </form>
                @endif
            </div>
        @else
            <!-- Assigned Cleaners Details -->
            <div class="assigned-cleaners-section">
                <h3>Assigned Cleaners</h3>
                @if($complaint->cleaners->isEmpty())
                <div class="alert alert-info">
                    No cleaners have been assigned to this complaint yet.
                </div>
                @else
                <ul class="assigned-cleaners-list">
                    @foreach($complaint->cleaners as $cleaner)
                    <li>
                        <img src="{{ $cleaner->getProfilePictureUrlAttribute() }}" alt="Cleaner Photo">
                        <div class="assigned-cleaners-details">
                            <strong>{{ $cleaner->cleaner_name }}</strong>
                            <span>Phone: {{ $cleaner->cleaner_phoneNo }}</span>
                            <span>Assigned By: {{ $cleaner->pivot->assignedBy->name ?? 'Unknown Supervisor' }}</span>
                            <span>Assigned Date: {{ \Carbon\Carbon::parse($cleaner->pivot->assigned_date)->format('d M Y') }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        @endif
    @endif

    <!-- Modal for Selecting Cleaners -->
    <div id="cleanerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Select Cleaners</h4>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Please select <span id="maxCleanersDisplay"></span> cleaner(s).</p>
                <ul class="cleaner-list">
                    @foreach($availableCleaners as $cleaner)
                    <li class="cleaner-item">
                        <label class="checkbox-wrapper-19">
                            <input type="checkbox" name="cleaners[]" value="{{ $cleaner->user_id }}" class="cleaner-checkbox">
                            <span class="check-box"></span>
                            @if($cleaner->profile_pic)
                            <img src="{{ asset($cleaner->profile_pic) }}" alt="Cleaner Photo">
                            @else
                            <img src="{{ asset('img/default_cleaner.png') }}" alt="Cleaner Photo">
                            @endif
                            <span class="cleaner-name">{{ $cleaner->cleaner_name }}</span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBtn">Save Selection</button>
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
<script>
// Enhanced JavaScript to handle 'no_of_cleaners' in the pivot table
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cleanerModal');
    const openModalBtn = document.getElementById('selectCleanersBtn');
    const closeModalElements = document.querySelectorAll('.close, #cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const assignBtn = document.getElementById('assignBtn');
    const selectedCleanersDiv = document.getElementById('selectedCleaners');
    const cleanerCheckboxes = document.querySelectorAll('.cleaner-checkbox');
    const noOfCleanersSelect = document.getElementById('no_of_cleaners');
    const maxCleanersDisplay = document.getElementById('maxCleanersDisplay');

    let maxCleaners = parseInt(noOfCleanersSelect.value);

    // Initialize maxCleanersDisplay
    maxCleanersDisplay.textContent = maxCleaners;

    // Update maxCleaners when number of cleaners changes
    noOfCleanersSelect.addEventListener('change', function () {
        maxCleaners = parseInt(noOfCleanersSelect.value);
        maxCleanersDisplay.textContent = maxCleaners;

        selectedCleanersDiv.innerHTML = ''; // Clear previous selections
        assignBtn.classList.add('d-none'); // Hide assign button

        cleanerCheckboxes.forEach(function (checkbox) {
            checkbox.checked = false;
            checkbox.disabled = false;
        });
    });

    // Function to update checkbox states
    function updateCheckboxStates() {
        const selectedCount = Array.from(cleanerCheckboxes).filter(cb => cb.checked).length;
        cleanerCheckboxes.forEach(function (checkbox) {
            checkbox.disabled = selectedCount >= maxCleaners && !checkbox.checked;
        });
    }

    // Add event listeners to checkboxes
    cleanerCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateCheckboxStates);
    });

    // Open Modal
    openModalBtn.addEventListener('click', function () {
        modal.style.display = 'block';
        updateCheckboxStates();
    });

    // Close Modal
    closeModalElements.forEach(function (element) {
        element.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    });

    // Save Selection
    saveBtn.addEventListener('click', function () {
        const selectedCleaners = Array.from(cleanerCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => ({
                user_id: checkbox.value,
                name: checkbox.parentElement.querySelector('.cleaner-name').textContent.trim(),
            }));

        if (selectedCleaners.length !== maxCleaners) {
            alert(`Please select exactly ${maxCleaners} cleaner(s).`);
            return;
        }

        selectedCleanersDiv.innerHTML = ''; // Clear previous selections

        selectedCleaners.forEach(function (cleaner) {
            // Create hidden input for each cleaner
            const cleanerInput = document.createElement('input');
            cleanerInput.type = 'hidden';
            cleanerInput.name = 'cleaners[]';
            cleanerInput.value = cleaner.user_id;
            selectedCleanersDiv.appendChild(cleanerInput);

            // Display selected cleaner's name
            const cleanerLabel = document.createElement('span');
            cleanerLabel.classList.add('selected-cleaner-name', 'badge', 'badge-info', 'mr-2');
            cleanerLabel.textContent = cleaner.name;
            selectedCleanersDiv.appendChild(cleanerLabel);
        });

        assignBtn.classList.remove('d-none');
        modal.style.display = 'none';
    });

    // Optional: Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
@endpush
