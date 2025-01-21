{{-- resources/views/supervisor/complaints/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Complaint Details')

@push('styles')
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" 
    />

    <!-- Custom Styles -->
    <style>
        .complaint-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .image-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            cursor: zoom-in;
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
        /* Status Badge Styles */
        .badge-pending {
            background-color: #f8d7da;
            color: #721c24;
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
            max-height: 80vh;
            overflow-y: auto; /* Scrollable if content is long */
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
            max-height: 300px; /* Additional scroll limit for cleaner list */
            overflow-y: auto;
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
        /* Image Zoom Modal */
        .image-zoom-modal {
            display: none; 
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            overflow: auto;
            align-items: center;
            justify-content: center;
        }
        .image-zoom-modal img {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .image-zoom-modal .close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
        }
        /* Loader / Spinner */
        .loader {
            display: none;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            @if ($complaint->comp_image)
                <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" 
                    alt="Complaint Image" 
                    class="complaint-image"
                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';" />
            @else
                <span>No Image Available</span>
                <img src="{{ asset('images/no-image.png') }}"
                    alt="Complaint Image"
                    class="complaint-image">
            @endif
        </div>

        <!-- Image Zoom Modal -->
        <div id="imageZoomModal" class="image-zoom-modal">
            <span class="close" id="zoomCloseBtn">&times;</span>
            <img id="zoomedImage" src="" alt="Zoomed Complaint Image">
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <h2>{{ $complaint->comp_desc }}</h2>
            <p class="meta"><strong>Location:</strong> {{ $complaint->comp_location }}</p>
            <p class="meta"><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</p>
            <p class="meta"><strong>Status:</strong>
                @if($complaint->comp_status == 'pending')
                    <span class="badge-pending">{{ ucfirst($complaint->comp_status) }}</span>
                @elseif($complaint->comp_status == 'ongoing')
                    <span class="badge-ongoing">{{ ucfirst($complaint->comp_status) }}</span>
                @else
                    <span class="badge-completed">{{ ucfirst($complaint->comp_status) }}</span>
                @endif
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
            <!-- Check if any cleaner is available -->
            @if($availableCleaners->isEmpty())
                <div class="alert alert-warning mt-4">
                    Oops! No cleaners are currently available. Please try again later.
                </div>
            @else
                <!-- Assign Cleaners Form -->
                <form id="assign-cleaners-form" action="{{ route('supervisor.complaints.assign-cleaner', $complaint->id) }}" method="POST">
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

                    <button type="button" id="selectCleanersBtn" class="btn btn-primary">
                        Select Cleaners
                    </button>
                    <p id="selectionFeedback" style="margin-top: 10px; color: red;"></p>
                    <div id="selectedCleaners" class="mt-3"></div>

                    <!-- Assign button is hidden/disabled until correct number of cleaners is selected -->
                    <button type="submit" class="btn btn-success mt-2 d-none" id="assignBtn" disabled>Assign</button>
                </form>
            @endif
            <!-- Loader / Spinner -->
            <div id="loader" class="loader"></div>
        @else
            <!-- If Complaint is Ongoing or Completed -->
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
                                <div class="assigned-cleaners-details">
                                    <strong>{{ $cleaner->cleaner_name }}</strong>
                                    <span>Phone: {{ $cleaner->cleaner_phoneNo }}</span>
                                    <span>Assigned by: {{ $complaint->supervisor->name ?? 'Unknown Officer' }}</span>
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
                            <label>
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
                <button type="button" class="btn btn-primary" id="saveBtn" disabled>Save Selection</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include any additional JS files via Vite or other bundlers if needed --}}
@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
    'resources/supervisor/complaint.js',
])

{{-- Custom Script for Modal and Image Zoom --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    /**
     * ELEMENT REFERENCES
     */
    const modal = document.getElementById('cleanerModal');
    const selectCleanersBtn = document.getElementById('selectCleanersBtn');
    const closeModalElements = document.querySelectorAll('.close, #cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const assignBtn = document.getElementById('assignBtn');
    const selectedCleanersDiv = document.getElementById('selectedCleaners');
    const cleanerCheckboxes = document.querySelectorAll('.cleaner-checkbox');
    const noOfCleanersSelect = document.getElementById('no_of_cleaners');
    const selectionFeedback = document.getElementById('selectionFeedback');
    const maxCleanersDisplay = document.getElementById('maxCleanersDisplay');
    const loader = document.getElementById('loader');

    // Parse initial max cleaners
    let maxCleaners = noOfCleanersSelect ? parseInt(noOfCleanersSelect.value) : 1;
    if (maxCleanersDisplay) maxCleanersDisplay.textContent = maxCleaners;

    /**
     * LOADER / SPINNER UTILS
     */
    function showLoader() {
        if (loader) loader.style.display = 'block';
    }
    function hideLoader() {
        if (loader) loader.style.display = 'none';
    }

    /**
     * IMAGE ZOOM LOGIC
     */
    window.openImageModal = function (src) {
        const modalZoom = document.getElementById('imageZoomModal');
        const zoomedImage = document.getElementById('zoomedImage');
        const zoomCloseBtn = document.getElementById('zoomCloseBtn');
        if (!modalZoom || !zoomedImage || !zoomCloseBtn) return;

        zoomedImage.src = src;
        modalZoom.style.display = 'flex';

        zoomCloseBtn.onclick = function() {
            modalZoom.style.display = 'none';
        };
        // Close if user clicks outside the zoomed image area
        modalZoom.onclick = function(e) {
            if (e.target === modalZoom) {
                modalZoom.style.display = 'none';
            }
        };
    };

    /**
     * CLEANER SELECTION FEEDBACK
     */
    function updateCleanerFeedback() {
        const selectedCount = [...cleanerCheckboxes].filter(cb => cb.checked).length;
        if (selectedCount < maxCleaners) {
            selectionFeedback.textContent = `Select ${maxCleaners - selectedCount} more cleaner(s).`;
            saveBtn.disabled = true;
        } else if (selectedCount > maxCleaners) {
            selectionFeedback.textContent = `Please deselect ${selectedCount - maxCleaners} cleaner(s).`;
            saveBtn.disabled = true;
        } else {
            selectionFeedback.textContent = '';
            saveBtn.disabled = false;
        }
    }

    function updateCheckboxStates() {
        // Disable or enable checkboxes if we have reached the max limit
        const selectedCount = [...cleanerCheckboxes].filter(cb => cb.checked).length;
        cleanerCheckboxes.forEach(checkbox => {
            // If we've reached maxCleaners, disable unchecked ones
            checkbox.disabled = selectedCount >= maxCleaners && !checkbox.checked;
        });
    }

    /**
     * ON CHANGE: NUMBER OF CLEANERS
     */
    if (noOfCleanersSelect) {
        noOfCleanersSelect.addEventListener('change', function () {
            maxCleaners = parseInt(this.value);
            maxCleanersDisplay.textContent = maxCleaners;
            selectionFeedback.textContent = `Select ${maxCleaners} cleaner(s).`;
            saveBtn.disabled = true;
            assignBtn.classList.add('d-none');
            assignBtn.disabled = true;

            // Reset checkboxes
            cleanerCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.disabled = false;
            });
            selectedCleanersDiv.innerHTML = '';
        });
    }

    /**
     * CHECKBOX EVENT LISTENERS
     */
    cleanerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateCleanerFeedback();
            updateCheckboxStates();
        });
    });

    /**
     * MODAL OPEN / CLOSE
     */
    if (selectCleanersBtn) {
        selectCleanersBtn.addEventListener('click', function () {
            if (modal) {
                modal.style.display = 'block';
                updateCleanerFeedback();
                updateCheckboxStates();
            }
        });
    }

    closeModalElements.forEach(el => {
        el.addEventListener('click', () => {
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal if user clicks outside content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    /**
     * SAVE SELECTION
     */
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            const selectedCheckboxes = [...cleanerCheckboxes].filter(cb => cb.checked);
            if (selectedCheckboxes.length !== maxCleaners) {
                alert(`Please select exactly ${maxCleaners} cleaner(s).`);
                return;
            }
            // Clear old selections
            selectedCleanersDiv.innerHTML = '';

            // Create hidden inputs for each selected cleaner
            selectedCheckboxes.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'cleaners[]';
                hiddenInput.value = checkbox.value;
                selectedCleanersDiv.appendChild(hiddenInput);

                // Display the selected cleaner name
                const cleanerName = checkbox.parentElement.querySelector('.cleaner-name').textContent.trim();
                const cleanerLabel = document.createElement('span');
                cleanerLabel.classList.add('selected-cleaner-name', 'badge', 'badge-info', 'mr-2');
                cleanerLabel.textContent = cleanerName;
                selectedCleanersDiv.appendChild(cleanerLabel);
            });

            // Reveal the Assign button
            assignBtn.classList.remove('d-none');
            assignBtn.disabled = false;
            modal.style.display = 'none';
        });
    }

  
    const assignForm = document.getElementById('assign-cleaners-form');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoader();
            const formData = new FormData(assignForm);
            fetch(assignForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                hideLoader();
                if (data.success) {
                    // handle success
                    location.reload();
                } else {
                    alert(data.message || 'An error occurred.');
                }
            })
            .catch(() => {
                hideLoader();
                alert('An error occurred. Please try again.');
            });
        });
    }

});
</script>
@endpush
