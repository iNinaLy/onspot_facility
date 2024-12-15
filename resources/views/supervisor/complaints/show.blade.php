@extends('layouts.app')

@section('title', 'Complaint Details')

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="alert alert-danger">
    <ul>
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
        <span class="badge {{ $complaint->comp_status == 'pending' ? 'status-pending' : ($complaint->comp_status == 'ongoing' ? 'status-ongoing' : 'status-completed') }}">
          {{ ucfirst($complaint->comp_status) }}
        </span>
      </p>
      <textarea rows="4" readonly>{{ $complaint->comp_desc }}</textarea>

      <!-- Complaint By Section -->
      <div class="complaint-by">
        Complaint by:
        <img src="{{ asset('img/profile_pic') }}" alt="User Profile">
        <span>{{ $complaint->user->name ?? 'Unknown User' }}</span>
      </div>
    </div>
  </div>

  <!-- Assign Section -->
@if (Auth::user()->role == 'supervisor' && $complaint->comp_status == 'pending')
<div class="assign-section">
    <h3>Assign Cleaners</h3>
    @if($availableCleaners->isEmpty())
    <div class="alert alert-warning">
        Oops! No cleaners are currently available. Please try again later.
    </div>
    @else
    <p>Available cleaners: <strong>{{ $availableCleaners->count() }}</strong></p>
    <form action="{{ route('supervisor.complaints.assign-cleaner', $complaint->id) }}" method="POST">
        @csrf
        <div class="assign-form">
            <label for="no_of_cleaners">Number of Cleaners:</label>
            <select name="no_of_cleaners" id="no_of_cleaners" class="form-select">
                @for ($i = 1; $i <= min($availableCleaners->count(), 3); $i++)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="button" id="selectCleanersBtn" class="btn-primary">Select Cleaners</button>
        <div id="selectedCleaners"></div>
        <button type="submit" class="btn-primary" style="display: none;" id="assignBtn">Assign</button>
    </form>
    @endif
</div>
@endif

<!-- Modal -->
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
                        <input type="checkbox" value="{{ $cleaner->user_id }}" class="cleaner-checkbox">
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
            <button type="button" class="btn btn-primary-modal" id="saveBtn">Save Selection</button>
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

        // Update maxCleaners when number of cleaners changes
        noOfCleanersSelect.addEventListener('change', function () {
            maxCleaners = parseInt(noOfCleanersSelect.value);
            maxCleanersDisplay.textContent = maxCleaners;

            selectedCleanersDiv.innerHTML = ''; // Clear previous selections
            assignBtn.style.display = 'none'; // Hide assign button

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
            maxCleanersDisplay.textContent = maxCleaners;
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
                const cleanerInput = document.createElement('input');
                cleanerInput.type = 'hidden';
                cleanerInput.name = 'cleaners[]';
                cleanerInput.value = cleaner.user_id;
                selectedCleanersDiv.appendChild(cleanerInput);

                const cleanerLabel = document.createElement('span');
                cleanerLabel.classList.add('selected-cleaner-name');
                cleanerLabel.textContent = cleaner.name;
                selectedCleanersDiv.appendChild(cleanerLabel);
            });

            assignBtn.style.display = 'inline-block';
            modal.style.display = 'none';
        });

        maxCleanersDisplay.textContent = maxCleaners; // Initialize display
    });
</script>
@endpush
