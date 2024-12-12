@extends('layouts.app')

@section('title', 'Recent Complaints')

@push('styles')
    <!-- External Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
<div class="container">
  <!-- Header with Filters on the Right -->
  <div class="header-container">
    <h1 class="heading">Recent Complaints</h1>

    <!-- Filter Form -->
    <form id="filter-form" class="filter-container" method="GET" action="{{ route('supervisor.complaints.index') }}">
      <label for="status-filter">Status:</label>
      <select id="status-filter" name="status">
        <option value="">All</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
      </select>

      <label for="date-filter">Date:</label>
      <input type="date" id="date-filter" name="date" value="{{ request('date') }}" />

      <button type="submit" class="view-details-btn">Filter</button>
    </form>
  </div>

  <!-- Complaints List -->
  @foreach($complaints as $complaint)
    <div class="complaint-card"
         data-complaint-id="{{ $complaint->id }}"
         data-status="{{ strtolower($complaint->comp_status) }}"
         data-date="{{ $complaint->comp_date }}">

      <!-- New Badge for Complaints Updated in Last 24 Hours -->
      @if($complaint->updated_at->diffInHours(now()) <= 24)
        <div class="new-badge">New</div>
      @endif

      <div class="complaint-image-container">
          @if ($complaint->comp_image)
            <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" alt="Complaint Image" class="complaint-image" />
          @else
            <span>No Image Available</span>
          @endif
      </div>

      <div class="complaint-details">
        <h3 class="complaint-title">{{ $complaint->comp_desc }}</h3>
        <p class="complaint-meta">Location: {{ $complaint->comp_location }}</p>
        <p class="complaint-meta">Date: {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</p>

        <span class="complaint-status
          @if(strtolower($complaint->comp_status) == 'pending') status-pending
          @elseif(strtolower($complaint->comp_status) == 'ongoing') status-ongoing
          @elseif(strtolower($complaint->comp_status) == 'completed') status-completed
          @endif">
          {{ ucfirst($complaint->comp_status) }}
        </span>

        <a href="{{ route('supervisor.complaints.show', $complaint->id) }}" class="view-details-btn">View Details</a>

        <!-- Show Edit Button if the logged-in supervisor assigned the complaint -->
        @if($complaint->cleaners->isNotEmpty() && $complaint->cleaners->first()->pivot->assigned_by == Auth::id())
          <a href="{{ route('supervisor.complaints.edit', $complaint->id) }}" class="edit-btn mt-2">Edit</a>
        @endif
      </div>
    </div>
  @endforeach

  <!-- Pagination Links -->
  <div class="mt-6">
    {{ $complaints->appends(request()->query())->links() }}
  </div>
</div>
@endsection

@push('scripts')
    <!-- Include compiled Supervisor JavaScript Files using Vite -->
    @vite([
        'resources/js/supervisor/app.js',
        'resources/js/supervisor/dashboard.js',
        'resources/js/supervisor/complaint.js',
    ])
@endpush
