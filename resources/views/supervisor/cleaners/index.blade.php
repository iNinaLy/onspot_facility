<x-app-layout>
    <title>{{ config('app.name','OnSpot Facility') }}</title>
    <link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">>

    <head>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <style>
            :root {
                --primary-color: #2E5675;
                --secondary-color: #4caf50;
                --available-color: #2196f3;
                --unavailable-color: #f44336;
            }

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
                color: var(--primary-color);
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
                background-color: var(--primary-color);
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

            /* Cleaner Overview Section */
            .cleaner-overview {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 15px;
            }

            .overview-box {
                flex: 1;
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(5px);
                border-radius: 15px;
                padding: 15px 20px;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease;
            }

            .overview-box:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }

            .overview-number {
                font-size: 1.6rem;
                font-weight: bold;
            }

            .overview-label {
                font-size: 0.9rem;
                font-weight: 500;
                color: #555;
            }

            .overview-total .overview-number {
                color: var(--secondary-color);
            }

            .overview-available .overview-number {
                color: var(--available-color);
            }

            .overview-unavailable .overview-number {
                color: var(--unavailable-color);
            }

            /* Cleaner Card Styling */
            .cleaner-card {
                width: 220px;
                background: #ffffff;
                border-radius: 15px;
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
                padding: 25px;
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                cursor: pointer;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .cleaner-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }

            .profile-pic {
                width: 90px;
                height: 90px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 15px;
                border: 4px solid var(--available-color);
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            }

            .cleaner-name {
                font-size: 1.15rem;
                font-weight: 600;
                color: #1f2937;
                margin: 10px 0;
            }

            .cleaner-status {
                font-size: 0.9rem;
                font-weight: 500;
                padding: 5px 15px;
                border-radius: 15px;
                display: inline-block;
                margin-top: 8px;
            }

            .status-available {
                background-color: #e3fcec;
                color: var(--secondary-color);
            }

            .status-unavailable {
                background-color: #fdecea;
                color: var(--unavailable-color);
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 30px;
                justify-items: center;
            }

            /* Redesigned Modal Styling */
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                justify-content: center;
                align-items: center;
                z-index: 100;
            }

            .modal-content {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 25px;
                background: rgba(255, 255, 255, 0.95);
                border-radius: 15px;
                max-width: 400px;
                text-align: center;
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            }

            .profile-pic-large {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                margin-bottom: 15px;
                border: 4px solid var(--available-color);
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            }

            .modal-cleaner-name {
                font-size: 1.5rem;
                font-weight: bold;
                color: #1f2937;
                margin-bottom: 10px;
            }

            .modal-cleaner-details {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100%;
                margin-bottom: 20px;
            }

            .detail-item {
                display: flex;
                align-items: center;
                font-size: 1rem;
                color: #555;
                gap: 10px;
            }

            .detail-icon {
                font-size: 1.25rem;
                color: var(--primary-color);
            }

            .status-available {
                background-color: #d4f8e8; /* Soft green background */
                color: #28a745; /* Darker green text */
            }

            .status-unavailable {
                background-color: #fde2e2; /* Soft red background */
                color: #e3342f; /* Darker red text */
            }

            /* Status Indicator Styles */
            .status-indicator {
                display: flex;
                align-items: center;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .status-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin-right: 8px;
            }

            .status-available .status-dot {
                background-color: #98e2b7; /* Pastel green for available */
                border-radius: 30%;
            }

            .status-unavailable .status-dot {
                background-color: #f8b2b2; /* Pastel red for unavailable */
                border-radius: 30%;
            }

            .status-available .status-text {
                color: #28a745; /* Darker green text for available */
            }

            .status-unavailable .status-text {
                color: #e3342f; /* Darker red text for unavailable */
            }



            .assign-button, .close-button {
                padding: 10px 20px;
                margin-top: 10px;
                font-size: 1rem;
                border-radius: 20px;
                cursor: pointer;
                border: none;
                width: 100%;
            }

            .assign-button {
                background-color: var(--secondary-color);
                color: white;
                transition: background-color 0.3s;
            }

            .assign-button:hover {
                background-color: #388e3c;
            }

            .close-button {
                background-color: var(--unavailable-color);
                color: white;
                transition: background-color 0.3s;
            }

            .close-button:hover {
                background-color: #d32f2f;
            }

            /* Assign Task Modal Styling */
            .assign-task-list {
                max-height: 300px;
                overflow-y: auto;
                width: 100%;
                text-align: left;
                margin-bottom: 20px;
            }

            .complaint-item {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .no-complaints-message {
                font-size: 1rem;
                color: #555;
                text-align: center;
                padding: 20px;
            }
        </style>
    </head>

    <div class="container">
        <div class="header-container">
            <h1 class="heading">Cleaners</h1>
            <form id="filter-form" class="filter-container">
                <input 
                    type="search" 
                    class="search-input" 
                    placeholder="Search for cleaners..." 
                    aria-label="Search" 
                    id="search-input" 
                />
                <select id="sort-status" class="sort-select">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </form>
        </div>

        <div class="cleaner-overview">
            <div class="overview-box overview-total">
                <div class="overview-number">{{ $totalCleaners }}</div>
                <div class="overview-label">Total Cleaners</div>
            </div>
            <div class="overview-box overview-available">
                <div class="overview-number">{{ $availableCount }}</div>
                <div class="overview-label">Available</div>
            </div>
            <div class="overview-box overview-unavailable">
                <div class="overview-number">{{ $unavailableCount }}</div>
                <div class="overview-label">Unavailable</div>
            </div>
        </div>

        <div class="grid" id="cleaner-grid">
            @foreach($cleaners as $cleaner)
            <div class="cleaner-card" data-id="{{ $cleaner->id }}" data-name="{{ strtolower($cleaner->cleaner_name) }}" data-status="{{ strtolower($cleaner->status) }}" data-phone="{{ $cleaner->cleaner_phoneNo }}" data-building="{{ $cleaner->building }}">
                @if($cleaner->profile_pic)
                    <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" alt="Cleaner Profile Picture" class="profile-pic">
                @else
                    <img src="{{ asset('images/default-placeholder.png') }}" alt="Cleaner Profile Picture" class="profile-pic">
                @endif
                <p class="cleaner-name">{{ $cleaner->cleaner_name }}</p>
                <p class="cleaner-status {{ $cleaner->status == 'available' ? 'status-available' : 'status-unavailable' }}">
                    {{ ucfirst($cleaner->status) }}
                </p> 
            </div>
            @endforeach
        </div>

        <!-- Cleaner Detail Modal -->
        <div class="modal" id="cleanerModal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeModal()" role="button" aria-label="Close">&times;</span>
                <img src="" alt="Cleaner Profile Picture" class="profile-pic-large" id="modalProfilePic">
                <h2 class="modal-cleaner-name" id="modalCleanerName"></h2>
                <div class="modal-cleaner-details">
                    <div class="detail-item">
                        <i class="fas fa-phone-alt detail-icon"></i>
                        <span>Contact Number:</span> <a href="#" id="modalCleanerPhoneLink"></a>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-building detail-icon"></i>
                        <span>Building:</span> <span id="modalCleanerBuilding"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-circle detail-icon" style="color: green;"></i>
                        <span>Status:</span> <span id="modalCleanerStatus"></span>
                    </div>
                </div>
                <button class="assign-button" onclick="assignTask()">Assign Task</button>
                <button class="close-button" onclick="closeModal()">Close</button>
            </div>
        </div>

        <!-- Assign Task Modal -->
        <div class="modal" id="assignTaskModal">
            <div class="modal-content">
                <h3>Assign Task to Cleaner</h3>
                <div class="assign-task-list" id="assignTaskList">
                    <!-- Pending complaints will be loaded here -->
                </div>
                <button class="close-button" onclick="closeAssignTaskModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        const cleanerGrid = document.getElementById('cleaner-grid');
        const modal = document.getElementById('cleanerModal');
        const assignTaskModal = document.getElementById('assignTaskModal');
        const assignTaskList = document.getElementById('assignTaskList');
        let selectedCleanerId;

        cleanerGrid.addEventListener('click', (event) => {
            const card = event.target.closest('.cleaner-card');
            if (card) openModal(card);
        });

        function openModal(card) {
            const cleanerName = card.querySelector('.cleaner-name').textContent;
            const cleanerPhone = card.getAttribute('data-phone');
            const cleanerBuilding = card.getAttribute('data-building');
            const cleanerStatus = card.querySelector('.cleaner-status').textContent.toLowerCase(); // Get status in lowercase
            const imgSrc = card.querySelector('.profile-pic').src;

            document.getElementById('modalCleanerName').textContent = cleanerName;
            document.getElementById('modalCleanerPhoneLink').textContent = cleanerPhone;
            document.getElementById('modalCleanerPhoneLink').href = `tel:${cleanerPhone}`;
            document.getElementById('modalCleanerBuilding').textContent = cleanerBuilding;

            // Update status indicator text and apply the correct class based on availability
            const statusElement = document.getElementById('modalCleanerStatus');
            statusElement.textContent = cleanerStatus.charAt(0).toUpperCase() + cleanerStatus.slice(1); // Capitalize status text

            if (cleanerStatus === 'available') {
                statusElement.classList.add('status-available');
                statusElement.classList.remove('status-unavailable');
            } else {
                statusElement.classList.add('status-unavailable');
                statusElement.classList.remove('status-available');
            }

            document.getElementById('modalProfilePic').src = imgSrc;

            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function assignTask() {
            selectedCleanerId = document.getElementById('modalCleanerName').textContent;
            loadPendingComplaints();
            assignTaskModal.style.display = 'flex';
        }

        function loadPendingComplaints() {
            fetch('/api/complaints/pending')
                .then(response => response.json())
                .then(data => {
                    console.log("Pending complaints:", data); // Debugging line
                    assignTaskList.innerHTML = '';
                    if (data.length === 0) {
                        assignTaskList.innerHTML = `<p class="no-complaints-message">No pending complaints available at the moment. All tasks are currently assigned. Please check back later!</p>`;
                    } else {
                        data.forEach(complaint => {
                            const complaintItem = document.createElement('div');
                            complaintItem.classList.add('complaint-item');
                            complaintItem.innerHTML = `
                                <span>${complaint.comp_desc} - ${complaint.comp_location}</span>
                                <button class="assign-button" onclick="assignCleanerToComplaint(${complaint.id})">Assign</button>
                            `;
                            assignTaskList.appendChild(complaintItem);
                        });
                    }
                })
                .catch(error => {
                    console.error("Error fetching complaints:", error); // Debugging line
                    assignTaskList.innerHTML = `<p class="no-complaints-message">There was an error fetching the complaints. Please try again later.</p>`;
                });
        }


        function assignCleanerToComplaint(complaintId) {
            fetch(`/complaints/assign/${complaintId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cleaner_id: selectedCleanerId
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Cleaner assigned successfully.');
                loadPendingComplaints();
            });
        }

        function closeAssignTaskModal() {
            assignTaskModal.style.display = 'none';
        }

        function filterCleaners() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedStatus = statusFilter.value;
            document.querySelectorAll('.cleaner-card').forEach(card => {
                const name = card.getAttribute('data-name');
                const status = card.getAttribute('data-status');
                const matchesSearch = name.includes(searchTerm);
                const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
                card.style.display = matchesSearch && matchesStatus ? 'flex' : 'none';
            });
        }

        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('sort-status');
        searchInput.addEventListener('input', filterCleaners);
        statusFilter.addEventListener('change', filterCleaners);


    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>