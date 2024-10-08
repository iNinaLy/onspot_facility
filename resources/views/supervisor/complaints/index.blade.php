<x-app-layout>
  <style>
    /* Navbar fix */
    nav {
      position: relative;
      z-index: 1;
    }

    /* Overall container adjustments */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      padding-top: 5rem;
    }

    /* Card styles */
    .complaint-card {
      display: flex;
      border-radius: 0.5rem;
      border: 1px solid #e2e8f0;
      background-color: #fbfbfb;
      padding: 5px;
      gap: 20px;
      height: auto;
      width: 100%;
      max-width: 1050px;
      margin-bottom: 20px;
      align-content: center;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
    }

    .complaint-card:hover {
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      transform: translateY(-3px);
    }

    /* Button styles */
    .view-details-btn {
      background-color: #2e5675;
      color: white;
      padding: 3px 15px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 85%;
      margin-top: 34px;
      text-decoration: none; /* To remove underline from <a> */
    }

    .view-details-btn:hover {
      background-color: #1f3c52;
    }

    /* Image styles */
    .complaint-image {
      width: 220px;
      height: 220px;
      object-fit: cover;
      border-radius: 1rem;
      background-color: #f0f4f8;
      border: 1px solid #e2e8f0;
    }

    /* Additional spacing for elements */
    .complaint-details {
      flex-grow: 1;
    }

    .complaint-title-status {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .complaint-title {
      margin: 0 0 5px 0;
      font-size: 1.125rem;
      font-weight: bold;
      color: #3c4858;
    }

    .complaint-meta {
      margin: 2px 0;
      color: #6c757d;
    }

    /* Status styles */
    .complaint-status {
      margin-top: 2px;
      padding: 1px 20px;
      border-radius: 20px;
      display: inline-block;
      font-size: 1rem;
      margin-bottom: 5px;
      margin-right: 15px;
    }

    .status-pending {
      background-color: #facdcd;
      color: #ae0c0c;
    }

    .status-resolved {
      background-color: #c3e6cb;
      color: #155724;
    }

    .status-in-progress {
      background-color: #b8e3f8;
      color: #0c5460;
    }

    .mb-6 {
      margin-bottom: 1.5rem;
    }

    /* List display style */
    .complaint-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* Filter container styles */
    .filter-container {
      display: flex;
      gap: 20px;
      align-items: center;
      margin-bottom: 20px;
      margin-right: 8.5rem;
      flex-direction: row-reverse;
    }

    .filter-container label {
      font-weight: bold;
    }

    #date-filter {
      padding: 5px;
      border-radius: 5px;
      border: 1px solid #e2e8f0;
    }

    /* Heading fix */
    h1 {
      font-size: 1.5rem; /* Larger font size */
      font-weight: 900; /* Bolder font */
      margin-top: 0;
      padding-top: 1rem;
      margin-left: 2.0rem;
    }
  </style>

  <div class="container px-4 py-12">
    <h1 class="text-5xl font-bold text-gray-900 mb-6">Recent Complaints</h1>

    <!-- Filter Form -->
    <div class="filter-container mb-6">
      <form id="filter-form">
        <label for="status-filter">Status:</label>
        <select id="status-filter">
          <option value="">All</option>
          <option value="pending">Pending</option>
          <option value="resolved">Resolved</option>
          <option value="in progress">In Progress</option>
        </select>

        <label for="date-filter">Date:</label>
        <input type="date" id="date-filter" />

        <button type="submit" class="view-details-btn">Filter</button>
      </form>
    </div>

    
    <!-- Display Complaints -->
@foreach($complaints as $complaint)
<div class="complaint-card" data-complaint-id="{{ $complaint->comp_id }}">
    <div class="complaint-image">
        @if ($complaint->comp_image)
            <img src="data:image/jpeg;base64,{{ base64_encode($complaint->comp_image) }}" alt="Complaint Image" class="complaint-image">
        @else
            <div class="h-full flex items-center justify-center">
                <span class="text-gray-400">No Image</span>
            </div>
        @endif
    </div>

    <div class="complaint-details">
        <div class="complaint-title-status">
            <h3 class="complaint-title">{{ $complaint->comp_desc }}</h3>
            <span class="complaint-status
                @if($complaint->comp_status == 'pending') status-pending
                @elseif($complaint->comp_status == 'resolved') status-resolved
                @elseif($complaint->comp_status == 'in progress') status-in-progress
                @endif">
                {{ ucfirst($complaint->comp_status) }}
            </span>
        </div>
        <p class="complaint-meta">Room, Floor</p>
        <p class="complaint-meta">{{ $complaint->comp_date }}</p>
        <a href="{{ route('supervisor.complaints.show', $complaint->id) }}" class="view-details-btn">View Details</a>


        </button>
    </div>
</div>
@endforeach

<!-- Full-Screen Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true" style="z-index: 1050;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintModalLabel">Complaint Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Description:</strong> <span id="complaint-desc"></span></p>
                <p><strong>Status:</strong> <span id="complaint-status"></span></p>
                <p><strong>Date:</strong> <span id="complaint-date"></span></p>
                <p><strong>Image:</strong></p>
                <img id="complaint-image" src="" alt="Complaint Image" class="complaint-image" style="max-width: 100%;">
            </div>
        </div>
    </div>
</div>


<script>
    // jQuery to handle the modal data population
    $('#complaintModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id');
        var desc = button.data('desc');
        var status = button.data('status');
        var date = button.data('date');
        var image = button.siblings('.complaint-image').find('img').attr('src'); // Get the image source

        var modal = $(this);
        modal.find('#complaint-desc').text(desc);
        modal.find('#complaint-status').text(status);
        modal.find('#complaint-date').text(date);
        modal.find('#complaint-image').attr('src', image || ''); // Set image source
    });
</script>



  <!-- JavaScript for filtering complaints -->
  <script>
    document.getElementById('filter-form').addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent the form from submitting

      const statusFilter = document.getElementById('status-filter').value.toLowerCase();
      const dateFilter = document.getElementById('date-filter').value; // Get the selected date
      const complaintCards = document.querySelectorAll('.complaint-card');

      complaintCards.forEach(card => {
        const status = card.querySelector('.complaint-status').textContent.toLowerCase(); // Get the status
        const date = card.querySelector('.complaint-meta:nth-child(3)').textContent; // Get the date

        // Check filters
        const statusMatch = !statusFilter || status === statusFilter; // Check status match
        const dateMatch = !dateFilter || date === dateFilter; // Check date match

        // Show or hide the card based on the filters
        if (statusMatch && dateMatch) {
          card.style.display = 'flex'; // Show card
        } else {
          card.style.display = 'none'; // Hide card
        }
      });
    });
  </script>
</x-app-layout>
