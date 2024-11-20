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
    height: 70px; /* Adjust this height if your navbar changes */
    display: flex;
    align-items: center;
    padding: 0 1rem;
    }

    .navbar-spacer {
    height: 70px; /* Same height as the navbar */
    width: 100%;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto 0; /* Add margin equal to the navbar height */
        padding: 1rem;
        background-color: white;
        border-radius: 12px;
    }



  /* Header with iOS-Style Back Button */
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
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
  }

  .form-select, .btn-primary {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
  }

  .form-select {
    border: 1px solid #dee2e6;
    color: #495057;
  }

  .btn-primary {
    background-color: #2E5675;
    color: white;
    border: none;
    transition: background-color 0.3s ease;
    cursor: pointer;
  }

  .btn-primary:hover {
    background-color: #234859;
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
      Opps! No cleaners are currently available. Please try again later.
    </div>
    @else
    <p>Available cleaners: <strong>{{ $availableCleaners->count() }}</strong></p>
    <form action="{{ route('supervisor.complaints.assign-cleaner', $complaint->id) }}" method="POST">
      @csrf
      <div class="assign-form">
        <select name="no_of_cleaners" class="form-select">
          @for ($i = 1; $i <= $availableCleaners->count(); $i++)
          <option value="{{ $i }}">{{ $i }}</option>
          @endfor
        </select>
        <button type="submit" class="btn-primary">Assign</button>
      </div>
    </form>
    @endif
  </div>
  @endif
</div>
@endsection
