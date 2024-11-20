@extends('layouts.app')

@section('title', 'Complaint Details')

@push('styles')
<style>
  /* Global Styles */
  body {
    background-color: #f8f9fa;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    padding-top: 0px;
  }

  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background-color: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    height: 70px;
    display: flex;
    align-items: center;
    padding: 0 1rem;
  }

  .navbar-spacer {
    height: 70px;
    width: 100%;
  }

  .container {
    max-width: 1200px;
    margin: 30px auto 0;
    padding: 1rem;
    background-color: white;
    border-radius: 12px;
  }

  /* Header with Back Button */
  .header-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .back-button {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #464c50;
    font-size: 1rem;
    font-weight: bold;
    padding: 0.5rem 0;
    gap: 0.5rem;
    transition: color 0.3s ease;
  }

  .back-button img {
    width: 16px;
    height: 16px;
    object-fit: contain;
  }

  .back-button:hover {
    color: #1c3d5a;
  }

  .heading {
    font-size: 1.5rem;
    font-weight: bold;
    color: #495057;
    margin: 0;
  }

  /* Flash Messages */
  .alert {
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border-left: 5px solid;
    font-size: 0.9rem;
  }

  .alert-success {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
  }

  .alert-warning {
    background-color: #fff3cd;
    border-color: #ffc107;
    color: #856404;
  }

  .alert-danger {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
  }

  .complaint-layout {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 2rem;
  }

  .image-section {
    background-color: #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    height: 100%;
  }

  .image-section img {
    max-width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 8px;
  }

  .details-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .details-section h2 {
    font-size: 1.25rem;
    font-weight: bold;
    color: #495057;
    margin-bottom: 0.5rem;
  }

  .details-section .meta {
    font-size: 0.85rem;
    color: #6c757d;
  }

  .details-section textarea {
    width: 100%;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.5rem;
    font-size: 0.875rem;
    background-color: #f8f9fa;
    resize: none;
  }

  .complaint-by {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.5rem;
  }

  .complaint-by img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    background-color: #e9ecef;
  }

  /* Status Badge */
  .badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 20px;
    text-transform: capitalize;
    font-weight: bold;
  }

  .status-pending {
    background-color: #f8d7da;
    color: #721c24;
  }

  .status-ongoing {
    background-color: #fff3cd;
    color: #856404;
  }

  .status-completed {
    background-color: #d4edda;
    color: #155724;
  }

  .assign-section {
    margin-top: 2rem;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background-color: white;
  }

  .assign-section h3 {
    font-size: 1rem;
    font-weight: bold;
    color: #2E5675;
    margin-bottom: 1rem;
  }

  .assign-form {
    margin-bottom: 1rem;
  }

  .assign-form label {
    font-size: 0.9rem;
    color: #495057;
    display: block;
    margin-bottom: 0.5rem;
  }

  .form-select, .btn-primary {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
  }

  .form-select {
    border: 1px solid #dee2e6;
    color: #495057;
    width: 100%;
  }

  .btn-primary {
    background-color: #2E5675;
    color: white;
    border: none;
    transition: background-color 0.3s ease;
    cursor: pointer;
    margin-top: 1rem;
  }

  .btn-primary:hover {
    background-color: #234859;
  }

  /* Modal Styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    background-color: rgba(0,0,0,0.5);
  }

  .modal-content {
    background-color: #fff;
    margin: 5% auto;
    border-radius: 12px;
    padding: 20px;
    width: 80%;
    max-width: 600px;
    position: relative;
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-header h4 {
    margin: 0;
    font-size: 1.25rem;
    color: #2E5675;
  }

  .close {
    font-size: 1.5rem;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
    transition: color 0.3s ease;
  }

  .close:hover {
    color: #000;
  }

  .modal-body {
    margin-top: 1rem;
    max-height: 400px;
    overflow-y: auto;
  }

  .cleaner-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .cleaner-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
  }

  .cleaner-item:last-child {
    border-bottom: none;
  }

  .cleaner-item img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 1rem;
  }

  .cleaner-name {
    font-size: 1rem;
    color: #495057;
  }

  /* Custom Checkbox Style */
  .checkbox-wrapper-19 {
    box-sizing: border-box;
    --background-color: #fff;
    --checkbox-height: 25px;
    display: flex;
    align-items: center;
    cursor: pointer;
  }

  .checkbox-wrapper-19 input[type=checkbox] {
    display: none;
  }

  .checkbox-wrapper-19 .check-box {
    height: var(--checkbox-height);
    width: var(--checkbox-height);
    background-color: transparent;
    border: calc(var(--checkbox-height) * .1) solid #000;
    border-radius: 5px;
    position: relative;
    display: inline-block;
    box-sizing: border-box;
    transition: border-color ease 0.2s;
    cursor: pointer;
    flex-shrink: 0;
  }

  .checkbox-wrapper-19 .check-box::before,
  .checkbox-wrapper-19 .check-box::after {
    box-sizing: border-box;
    position: absolute;
    height: 0;
    width: calc(var(--checkbox-height) * .2);
    background-color: #34b93d;
    display: inline-block;
    transform-origin: left top;
    border-radius: 5px;
    content: " ";
    transition: opacity ease 0.5s;
  }

  .checkbox-wrapper-19 .check-box::before {
    top: calc(var(--checkbox-height) * .72);
    left: calc(var(--checkbox-height) * .41);
    box-shadow: 0 0 0 calc(var(--checkbox-height) * .05) var(--background-color);
    transform: rotate(-135deg);
  }

  .checkbox-wrapper-19 .check-box::after {
    top: calc(var(--checkbox-height) * .37);
    left: calc(var(--checkbox-height) * .05);
    transform: rotate(-45deg);
  }

  .checkbox-wrapper-19 input[type=checkbox]:checked + .check-box {
    border-color: #34b93d;
  }

  .checkbox-wrapper-19 input[type=checkbox]:checked + .check-box::after {
    height: calc(var(--checkbox-height) / 2);
    animation: dothabottomcheck-19 0.2s ease 0s forwards;
  }

  .checkbox-wrapper-19 input[type=checkbox]:checked + .check-box::before {
    height: calc(var(--checkbox-height) * 1.2);
    animation: dothatopcheck-19 0.4s ease 0s forwards;
  }

  @keyframes dothabottomcheck-19 {
    0% {
      height: 0;
    }
    100% {
      height: calc(var(--checkbox-height) / 2);
    }
  }

  @keyframes dothatopcheck-19 {
    0% {
      height: 0;
    }
    50% {
      height: 0;
    }
    100% {
      height: calc(var(--checkbox-height) * 1.2);
    }
  }

  /* Modal Footer */
  .modal-footer {
    margin-top: 1rem;
    text-align: right;
  }

  .modal-footer .btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    margin-right: 0.5rem;
  }

  .btn-secondary:hover {
    background-color: #5a6268;
  }

  .btn-primary-modal {
    background-color: #2E5675;
    color: white;
    border: none;
  }

  .btn-primary-modal:hover {
    background-color: #234859;
  }

  /* Selected Cleaners Display */
  #selectedCleaners {
    margin-top: 1rem;
  }

  .selected-cleaner-name {
    display: inline-block;
    background-color: #e9ecef;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #495057;
  }
</style>
@endpush
@section('content')
<div class="navbar-spacer"></div> <!-- Spacer to push content below the navbar -->
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
        <label for="no_of_cleaners">Number of Cleaners (Max 3):</label>
        <select name="no_of_cleaners" id="no_of_cleaners" class="form-select">
          @for ($i = 1; $i <= min($availableCleaners->count(), 3); $i++)
          <option value="{{ $i }}">{{ $i }}</option>
          @endfor
        </select>
      </div>
      <button type="button" id="selectCleanersBtn" class="btn-primary">Select Cleaners</button>
      <!-- Selected Cleaners will be displayed here -->
      <div id="selectedCleaners"></div>
      <button type="submit" class="btn-primary" style="display: none;" id="assignBtn">Assign</button>
    </form>
    @endif
  </div>
  @endif
</div>

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
            <input type="checkbox" value="{{ $cleaner->id }}" class="cleaner-checkbox">
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
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
    noOfCleanersSelect.addEventListener('change', function() {
      maxCleaners = parseInt(noOfCleanersSelect.value);
      maxCleanersDisplay.textContent = maxCleaners;

      // Reset checkboxes
      cleanerCheckboxes.forEach(function(checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
        checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
      });
    });

    // Function to update checkbox states
    function updateCheckboxStates() {
      const selectedCount = Array.from(cleanerCheckboxes).filter(cb => cb.checked).length;
      if (selectedCount >= maxCleaners) {
        cleanerCheckboxes.forEach(function(checkbox) {
          if (!checkbox.checked) {
            checkbox.disabled = true;
            checkbox.parentElement.querySelector('.check-box').style.borderColor = '#ccc';
          }
        });
      } else {
        cleanerCheckboxes.forEach(function(checkbox) {
          checkbox.disabled = false;
          checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
        });
      }
    }

    // Add event listeners to checkboxes
    cleanerCheckboxes.forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        updateCheckboxStates();
      });
    });

    // Open Modal
    openModalBtn.addEventListener('click', function() {
      modal.style.display = 'block';

      // Reset checkboxes
      cleanerCheckboxes.forEach(function(checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
        checkbox.parentElement.querySelector('.check-box').style.borderColor = '#000';
      });

      // Update maxCleaners in case it changed
      maxCleaners = parseInt(noOfCleanersSelect.value);
      maxCleanersDisplay.textContent = maxCleaners;

      updateCheckboxStates();
    });

    // Close Modal
    closeModalElements.forEach(function(element) {
      element.addEventListener('click', function() {
        modal.style.display = 'none';
      });
    });

    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    });

    // Save Selection
    saveBtn.addEventListener('click', function() {
      const selectedCleaners = Array.from(cleanerCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => {
          return {
            id: checkbox.value,
            name: checkbox.parentElement.querySelector('.cleaner-name').textContent.trim()
          };
        });

      if (selectedCleaners.length !== maxCleaners) {
        alert(`Please select exactly ${maxCleaners} cleaner(s).`);
        return;
      }

      // Clear previous selections
      selectedCleanersDiv.innerHTML = '';

      // Append selected cleaners to the form
      selectedCleaners.forEach(function(cleaner) {
        const cleanerInput = document.createElement('input');
        cleanerInput.type = 'hidden';
        cleanerInput.name = 'cleaners[]'; // Adjusted to match controller
        cleanerInput.value = cleaner.id;
        selectedCleanersDiv.appendChild(cleanerInput);

        const cleanerLabel = document.createElement('span');
        cleanerLabel.classList.add('selected-cleaner-name');
        cleanerLabel.textContent = cleaner.name;
        selectedCleanersDiv.appendChild(cleanerLabel);
      });

      assignBtn.style.display = 'inline-block';

      // Close modal
      modal.style.display = 'none';
    });

    // Initialize max cleaners display
    maxCleanersDisplay.textContent = maxCleaners;
  });
</script>
@endpush