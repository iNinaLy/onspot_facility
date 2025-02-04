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
            align-items: flex-end;
            margin-top: 15px;
        }
        .complaint-by img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 0.8rem;
            border: 2px rgba(67, 109, 141, 0.7);
            font-size: 0.8rem;
            text-align: center;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s var(--transition-ease);
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
        .assignbtn {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
        }
        /* Overall Container */
        .assign-container {
            max-width: 700px;
            font-family: 'Inter', sans-serif;
            color: #333;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        /* Card Sections */
        .card-section {
            background: #fdfdfd00;
            border-radius: 16px;
            border: none;
            padding: 2rem;
            position: relative;
        }
        .hidden { display: none; }
        /* Fade Up Animation for the "Review" card */
        @keyframes fadeUp {
            0%   { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .fade-up {
            animation: fadeUp 0.4s ease forwards;
        }
        /* Titles & Form Controls */
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2e5675;
        }
        .form-group {
            margin-bottom: 1.3rem;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            margin-top: 1rem;
            color: #555;
            font-size: 0.9rem;
        }
        select.form-control {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 0.5rem;
            font-family: inherit;
            font-size: 0.95rem;
            background: rgb(255, 255, 255);
            outline: none;
        }
        /* Buttons */
        .btn {
            border: none;
            border-radius: 24px;
            padding: 0.6rem 1.4rem;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s;
        }
        .btn:hover {
            transform: scale(1.02);
        }
        .btn-primary {
            background-color: #a0d8ef;
            color: #fff;
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-success {
            --bs-btn-color: #fffdfd;
            --bs-btn-bg: #2e5675c7;
            --bs-btn-border-color: #edededda;
            --bs-btn-font-size: 0.9rem;
            --bs-btn-border-radius: 16px;
            --bs-border-color: #ffffff;
            --bs-btn-hover-color: #fffdfd;
            --bs-btn-hover-border-color: #2e5675;
            --bs-btn-hover-bg: #2E5675;
            --bs-btn-focus-shadow-rgb: 60, 153, 110;
            --bs-btn-transition: background-color 0.5s ease;
        }
        .btn-secondary {
            background-color: #f3d1dc; 
            color: #444;
        }
        /* Selected Cleaners (Review) */
        .selected-cleaners {
            background: #fcfcfc;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 1rem;
            min-height: 60px;
        }
        .cleaner-badge {
            display: inline-flex;
            align-items: center;
            background: rgb(231, 232, 232);
            border-radius: 2rem;
            margin: 0.25rem;
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
        }
        .remove-cleaner {
            cursor: pointer;
            color: rgb(215, 68, 70);
            margin-left: 0.5rem;
            font-weight: bold;
        }
        /* ========== MODAL ========== */
        .modal-overlay {
            display: flex;
            position: fixed;         
            top: 0; 
            left: 0;
            width: 100%; 
            height: 100%;
            background: rgba(242, 242, 247, 0.07); 
            backdrop-filter: blur(6px);            
            z-index: 9999;
            align-items: center;
            justify-content: center;
            opacity: 0;       
            pointer-events: none; 
            transition: opacity 0.3s;
        }
        .modal-overlay.active {
            opacity: 1;             
            pointer-events: auto;   
        }
        .modal-content {
            background: #fffaf7; 
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            padding: 2rem;
            position: relative;
            margin-top: -0.1rem;
            transform: scale(0.95) translateY(15px);
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .modal-header, .modal-body, .modal-footer {
            margin-bottom: 1rem;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .modal-close {
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            color: #999;
        }
        .modal-close:hover {
            color: #333;
        }
        /* Cleaner List in Modal */
        .cleaner-item input[type="checkbox"] {
            display: none;
        }
        .fancy-checkbox {
            position: relative;
            display: inline-block;
            width: 22px; 
            height: 22px;
            background: #fff;
            border: 2px solid rgb(68, 103, 117);
            border-radius: 6px;
            margin-right: 1rem;
            margin-left: 2rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background 0.2s, border 0.2s;
        }
        .fancy-checkbox.checked {
            background: rgb(39, 66, 80);
            border-color: rgb(26, 50, 62);
        }
        .fancy-checkbox::after {
            content: '';
            position: absolute;
            left: 5px; 
            top: 2px;
            width: 6px; 
            height: 12px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform 0.2s ease;
        }
        .fancy-checkbox.checked::after {
            transform: rotate(45deg) scale(1);
        }
        /* Additional instructions in modal */
        .modal-instructions {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
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
                Complaint by: 
                @if($complaint->user->profile_pic)
                    <img src="data:image/jpeg;base64,{{ base64_encode($complaint->user->profile_pic) }}"
                         alt="{{ $complaint->user->name }}" 
                         class="complaint-by img"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-image.png') }}';">
                @else
                    <img src="{{ asset('images/default-image.png') }}"
                         alt="Profile Image"
                         class="complaint-by img">
                @endif
                <span>{{ $complaint->user->name ?? 'Unknown User' }}</span>
            </div>
        </div>
    </div>

    <!-- Assign or Assigned Cleaners Section -->
    <div class="assign-container">
        <form id="assignForm" method="POST" action="{{ route('supervisor.complaints.assign-cleaner', $complaint->id) }}">
            @csrf

            <!-- Flash / Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(Auth::user()->role == 'supervisor')
                @if($complaint->comp_status == 'pending')
                    @if($availableCleaners->isEmpty())
                        <div class="alert alert-warning mt-4">
                            Oops! No cleaners are currently available. Please try again later.
                        </div>
                    @else
                        <div class="card-section">
                            <h2 class="card-title">Assign Cleaner</h2>
                            <!-- Display the total number of available cleaners here -->
                            <p style="font-size: 0.9rem; font-weight: 350; margin-top:0.3rem; margin-bottom: 0.5rem; color:rgb(84, 95, 110);">
                               <strong>{{ $availableCleaners->count() }}</strong> cleaner(s) available.
                            </p>
         
                            <label for="no_of_cleaners">Select number of cleaners</label>
                            <div class="form-group">
                                <select name="no_of_cleaners" id="no_of_cleaners" class="form-control">
                                    @for ($i = 1; $i <= $availableCleaners->count(); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <button type="button" id="openModalBtn" class="btn btn-primary">Select Cleaners</button>
                        </div>

                        <div class="card-section hidden" id="reviewSection">
                            <h2 class="card-title">Review & Confirm</h2>
                            <label>Selected Cleaner(s):</label>
                            <div class="selected-cleaners" id="selectedCleaners"></div>

                            <button type="submit" class="btn btn-success" style="margin-top: 2rem;" id="assignBtn" disabled>
                                Assign Cleaners
                            </button>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        This complaint has already been assigned to cleaners.
                    </div>
                @endif
            @endif

            <!-- ========== MODAL FOR SELECTING CLEANERS ========== -->
            @if(Auth::user()->role == 'supervisor' && $complaint->comp_status == 'pending' && !$availableCleaners->isEmpty())
                <div class="modal-overlay" id="modalOverlay">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Select Cleaners</h4>
                            <span class="modal-close" id="closeModalBtn">&times;</span>
                        </div>
                        <p class="modal-instructions" style="color: red;">
                            Please choose <span id="neededCount" style="font-weight:600;"></span> cleaner(s). 
                        </p>
                        <div class="modal-body">
                            <div class="cleaner-list" id="cleanerList">
                                @foreach($availableCleaners as $cleaner)
                                    <div class="cleaner-item">
                                        <div class="fancy-checkbox" data-id="{{ $cleaner->id }}"></div>
                                        
                                        <input type="checkbox"
                                               class="modal-cleaner-checkbox"
                                               id="cleaner-{{ $cleaner->id }}"
                                               value="{{ $cleaner->id }}">

                                        <label style="cursor:pointer; margin-bottom:0; margin-left:6px;" 
                                               for="cleaner-{{ $cleaner->id }}">
                                            {{ $cleaner->cleaner_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:0.5rem;">
                            <button type="button" class="btn btn-secondary" id="cancelModalBtn">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="saveModalBtn" disabled>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </form>
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
document.addEventListener('DOMContentLoaded', function () {

    const loader = document.getElementById('loader');
    function showLoader() {
        if (loader) loader.style.display = 'block';
    }
    function hideLoader() {
        if (loader) loader.style.display = 'none';
    }

    (function initImageZoom() {
        const modalZoom    = document.getElementById('imageZoomModal');
        const zoomedImage  = document.getElementById('zoomedImage');
        const zoomCloseBtn = document.getElementById('zoomCloseBtn');
        const complaintImg = document.querySelector('.complaint-image');

        if (!modalZoom || !zoomedImage || !zoomCloseBtn || !complaintImg) return;

        // When user clicks the complaint image, show zoom
        complaintImg.addEventListener('click', () => {
            zoomedImage.src = complaintImg.src;
            modalZoom.style.display = 'flex';
        });

        // Close zoom if user clicks close "X"
        zoomCloseBtn.onclick = function() {
            modalZoom.style.display = 'none';
        };

        // Also close if user clicks outside the zoomed image area
        modalZoom.addEventListener('click', (e) => {
            if (e.target === modalZoom) {
                modalZoom.style.display = 'none';
            }
        });
    })();

    // Element references
    const noOfCleanersSelect  = document.getElementById('no_of_cleaners');
    const openModalBtn        = document.getElementById('openModalBtn');
    const modalOverlay        = document.getElementById('modalOverlay');
    const closeModalBtn       = document.getElementById('closeModalBtn');
    const cancelModalBtn      = document.getElementById('cancelModalBtn');
    const saveModalBtn        = document.getElementById('saveModalBtn');
    const neededCount         = document.getElementById('neededCount');

    const modalCheckboxes     = document.querySelectorAll('.modal-cleaner-checkbox');
    const fancyCheckboxes     = document.querySelectorAll('.fancy-checkbox');

    const reviewSection       = document.getElementById('reviewSection');
    const selectedCleanersDiv = document.getElementById('selectedCleaners');
    const assignBtn           = document.getElementById('assignBtn');
    const assignForm          = document.getElementById('assignForm');

    // State
    let maxCleaners = noOfCleanersSelect ? parseInt(noOfCleanersSelect.value) : 1;
    let selectedCleaners = new Set();

    // Helper Functions
    function openModal() {
        if (modalOverlay) {
            modalOverlay.classList.add('active');
        }
    }
    function closeModal() {
        if (modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    }
    function updateNeededCountLabel() {
        if (neededCount) {
            neededCount.textContent = maxCleaners;
        }
    }
    function resetSelection() {
        selectedCleaners.clear();
        modalCheckboxes.forEach(cb => { cb.checked = false; });
        fancyCheckboxes.forEach(fc => fc.classList.remove('checked'));
        if (reviewSection) {
            reviewSection.classList.add('hidden');
            reviewSection.classList.remove('fade-up');
        }
        renderSelectedCleaners();
    }
    function revealReviewSection() {
        if (reviewSection && selectedCleaners.size === maxCleaners) {
            reviewSection.classList.remove('hidden');
            reviewSection.classList.add('fade-up');
        }
    }
    function validateReview() {
        if (assignBtn) {
            assignBtn.disabled = (selectedCleaners.size !== maxCleaners);
        }
    }
    function checkModalCount() {
        if (!saveModalBtn) return;
        const count = [...modalCheckboxes].filter(cb => cb.checked).length;
        saveModalBtn.disabled = (count !== maxCleaners);
    }
    function toggleFancyCheckbox(fc, checked) {
        if (checked) {
            fc.classList.add('checked');
        } else {
            fc.classList.remove('checked');
        }
    }
    function renderSelectedCleaners() {
        if (!selectedCleanersDiv) return;
        selectedCleanersDiv.innerHTML = '';
        selectedCleaners.forEach(id => {
            const cb = document.getElementById(`cleaner-${id}`);
            if (!cb) return;
            const labelEl = cb.closest('.cleaner-item')?.querySelector(`label[for="cleaner-${id}"]`);
            const label = labelEl ? labelEl.textContent.trim() : 'Unknown Cleaner';
            const badge = document.createElement('div');
            badge.classList.add('cleaner-badge');
            badge.innerHTML = `
                <span>${label}</span>
                <span class="remove-cleaner" data-id="${id}">&times;</span>
            `;
            selectedCleanersDiv.appendChild(badge);
        });
        validateReview();
    }

    if (noOfCleanersSelect) {
        noOfCleanersSelect.addEventListener('change', () => {
            maxCleaners = parseInt(noOfCleanersSelect.value);
            updateNeededCountLabel();
            resetSelection();
        });
    }

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            updateNeededCountLabel();
            // Sync modal with current selections
            modalCheckboxes.forEach(cb => {
                const isChecked = selectedCleaners.has(cb.value);
                cb.checked = isChecked;
                toggleFancyCheckbox(
                    document.querySelector(`.fancy-checkbox[data-id="${cb.value}"]`),
                    isChecked
                );
            });
            checkModalCount();
            openModal();
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    if (cancelModalBtn) {
        cancelModalBtn.addEventListener('click', closeModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    fancyCheckboxes.forEach(fc => {
        fc.addEventListener('click', () => {
            const id = fc.dataset.id;
            const cb = document.getElementById(`cleaner-${id}`);
            if (!cb) return;
            const newState = !cb.checked;
            if (newState) {
                const count = [...modalCheckboxes].filter(c => c.checked).length;
                if (count >= maxCleaners) return;
            }
            cb.checked = newState;
            toggleFancyCheckbox(fc, newState);
            checkModalCount();
        });
    });

    if (saveModalBtn) {
        saveModalBtn.addEventListener('click', () => {
            selectedCleaners.clear();
            modalCheckboxes.forEach(cb => {
                if (cb.checked) {
                    selectedCleaners.add(cb.value);
                }
            });
            renderSelectedCleaners();
            revealReviewSection();
            closeModal();
        });
    }

    if (selectedCleanersDiv) {
        selectedCleanersDiv.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-cleaner')) {
                const id = e.target.dataset.id;
                selectedCleaners.delete(id);
                const cb = document.getElementById(`cleaner-${id}`);
                if (cb) {
                    cb.checked = false;
                    toggleFancyCheckbox(
                        document.querySelector(`.fancy-checkbox[data-id="${id}"]`),
                        false
                    );
                }
                renderSelectedCleaners();
            }
        });
    }

    if (assignForm) {
        assignForm.addEventListener('submit', () => {
            showLoader();
            document.querySelectorAll('input[name="cleaners[]"]').forEach(el => el.remove());
            selectedCleaners.forEach(id => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'cleaners[]';
                hidden.value = id;
                assignForm.appendChild(hidden);
            });
        });
    }

    updateNeededCountLabel();
    resetSelection();
});
</script>

@endpush
