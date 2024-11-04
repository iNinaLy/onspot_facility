<x-app-layout>
  <style>
    /* General Container Styles */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    /* Header with Filters on the Right */
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .heading {
      font-size: 2rem;
      font-weight: bold;
      color: #2E5675;
    }

    /* Filter Form Styles */
    .filter-container {
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-container select,
    .filter-container input {
      padding: 0.5rem;
      border-radius: 0.5rem;
      border: 1px solid #d1d5db;
      font-size: 0.875rem;
      width: 150px;
    }

    .view-details-btn {
      background-color: #2E5675;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-weight: 600;
      transition: background-color 0.3s ease;
      border: none;
      cursor: pointer;
    }

    .view-details-btn:hover {
      background-color: #1f3c52;
    }

    /* Complaint Card Styles */
    .complaint-card {
      display: flex;
      background-color: white;
      border-radius: 1rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      margin-bottom: 1.5rem;
    }

    .complaint-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .complaint-image-container {
      flex: 0 0 220px;
      height: 220px;
      background-color: #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .complaint-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .complaint-details {
      flex-grow: 1;
      padding: 1.5rem;
    }

    .complaint-title {
      font-size: 1.25rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #1f2937;
    }

    .complaint-meta {
      color: #6b7280;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }

    .complaint-status {
      display: inline-block;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      border-radius: 9999px;
      margin-top: 0.5rem;
    }

    /* Updated Status Styles */
    .status-pending {
      background-color: #fee2e2;
      color: #b91c1c;
    }

    .status-ongoing {
      background-color: #fef3c7;
      color: #ca8a04;
    }

    .status-completed {
      background-color: #d1fae5;
      color: #065f46;
    }

    /* Pagination Styles */
    .pagination {
      display: flex;
      justify-content: center;
      list-style: none;
      padding: 0;
    }

    .pagination li {
      margin: 0 5px;
    }

    .pagination a,
    .pagination span {
      color: #2E5675;
      padding: 8px 12px;
      text-decoration: none;
      border: 1px solid #d1d5db;
      border-radius: 5px;
    }

    .pagination .active span {
      background-color: #2E5675;
      color: white;
      border-color: #2E5675;
    }

    .pagination a:hover {
      background-color: #f0f0f0;
    }
  </style>

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
        </div>
      </div>
    @endforeach

    <!-- Pagination Links -->
    <div class="mt-6">
      {{ $complaints->appends(request()->query())->links() }}
    </div>
  </div>

</x-app-layout>
