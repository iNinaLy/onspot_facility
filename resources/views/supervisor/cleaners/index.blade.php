<x-app-layout>
    <title>{{ config('app.name', 'OnSpot Facility') }}</title>
    <link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

            /* Specific Colors for Each Number */
            .overview-total .overview-number {
                color: #4caf50;
            }

            .overview-available .overview-number {
                color: #2196f3;
            }

            .overview-unavailable .overview-number {
                color: #f44336;
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
                border: 4px solid #3b82f6;
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
                color: #27ae60;
            }

            .status-unavailable {
                background-color: #fdecea;
                color: #e74c3c;
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
                border: 4px solid #3b82f6;
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
                color: #2e5675;
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
                background-color: #4caf50;
                color: white;
                transition: background-color 0.3s;
            }

            .assign-button:hover {
                background-color: #388e3c;
            }

            .close-button {
                background-color: #f44336;
                color: white;
                transition: background-color 0.3s;
            }

            .close-button:hover {
                background-color: #d32f2f;
            }
        </style>
    </head>

    <div class="container">
        <!-- Header with Filters on the Right -->
        <div class="header-container">
            <h1 class="heading">Cleaners</h1>
            <!-- Filter Form -->
            <form id="filter-form" class="filter-container">
                <div class="search-bar">
                    <input 
                        type="search" 
                        class="search-input" 
                        placeholder="Search for cleaners..." 
                        aria-label="Search" 
                        id="search-input" 
                    />
                    <span class="search-icon-container">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <select id="sort-status" class="sort-select">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </form>
        </div>

        <!-- Cleaner Overview -->
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

        <!-- Cleaner Cards -->
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

        <!-- Cleaner Details Modal -->
        <div class="modal" id="cleanerModal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeCleanerModal()">&times;</span>
                <img src="" alt="Cleaner Profile Picture" class="profile-pic-large" id="modalProfilePic">
                <h2 id="modalCleanerName"></h2>
                <p><strong>Contact:</strong> <a href="#" id="modalCleanerPhone"></a></p>
                <p><strong>Building:</strong> <span id="modalCleanerBuilding"></span></p>
                <p><strong>Status:</strong> <span id="modalCleanerStatus"></span></p>
                <button class="assign-button" onclick="openAssignTaskModal()">Assign Task</button>
                <button class="close-button" onclick="closeCleanerModal()">Close</button>
            </div>
        </div>

        <!-- Assign Task Modal -->
        <div class="modal" id="assignTaskModal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeAssignTaskModal()">&times;</span>
                <h2>Pending Complaints</h2>
                <div id="complaintList"></div>
                <button class="close-button" onclick="closeAssignTaskModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        let selectedCleanerId = null;
        let selectedComplaintId = null;

        // Open Cleaner Modal
        function openCleanerModal(cleaner) {
            selectedCleanerId = cleaner.id;
            document.getElementById('modalProfilePic').src = cleaner.profile_pic ? `data:image/jpeg;base64,${cleaner.profile_pic}` : '{{ asset("images/default-placeholder.png") }}';
            document.getElementById('modalCleanerName').textContent = cleaner.cleaner_name;
            document.getElementById('modalCleanerPhone').textContent = cleaner.cleaner_phoneNo;
            document.getElementById('modalCleanerPhone').href = `tel:${cleaner.cleaner_phoneNo}`;
            document.getElementById('modalCleanerBuilding').textContent = cleaner.building;
            document.getElementById('modalCleanerStatus').textContent = cleaner.status.charAt(0).toUpperCase() + cleaner.status.slice(1);
            document.getElementById('cleanerModal').style.display = 'flex';
        }

        function closeCleanerModal() {
            document.getElementById('cleanerModal').style.display = 'none';
        }

        // Fetch and Open Assign Task Modal
        function openAssignTaskModal() {
            fetch('/pending-complaints')
                .then(response => response.json())
                .then(data => {
                    const complaintList = document.getElementById('complaintList');
                    complaintList.innerHTML = '';

                    if (data.length === 0) {
                        complaintList.innerHTML = '<p>No pending complaints found.</p>';
                        return;
                    }

                    data.forEach(complaint => {
                        const complaintItem = document.createElement('div');
                        complaintItem.classList.add('complaint-item');
                        complaintItem.innerHTML = `
                            <p><strong>ID:</strong> ${complaint.id}</p>
                            <p><strong>Description:</strong> ${complaint.comp_desc}</p>
                            <button onclick="assignComplaintToCleaner(${complaint.id})">Assign to Cleaner</button>
                        `;
                        complaintList.appendChild(complaintItem);
                    });

                    document.getElementById('assignTaskModal').style.display = 'flex';
                });
        }

        function assignComplaintToCleaner(complaintId) {
            selectedComplaintId = complaintId;

            fetch('/assign-cleaner', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    complaint_id: selectedComplaintId,
                    cleaner_id: selectedCleanerId
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                closeAssignTaskModal();
            })
            .catch(error => console.error('Error:', error));
        }

        function closeAssignTaskModal() {
            document.getElementById('assignTaskModal').style.display = 'none';
        }
    </script>
</x-app-layout>
