<x-app-layout>
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

        /* Modal Styling */
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
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            text-align: center;
            position: relative;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: #f44336;
        }

        .assign-button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        .assign-button:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
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

    <!-- Modal for Cleaner Details -->
    <div class="modal" id="cleanerModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <img src="" alt="Cleaner Profile Picture" class="profile-pic" id="modalProfilePic">
            <h2 class="cleaner-name" id="modalCleanerName"></h2>
            <div class="contact-details">
                <p><strong>Contact Number:</strong> <a href="#" id="modalCleanerPhoneLink"></a></p>
                <p><strong>Building:</strong> <span id="modalCleanerBuilding"></span></p>
                <p><strong>Status:</strong> <span id="modalCleanerStatus"></span></p>
            </div>
            <button class="assign-button" onclick="openTaskModal()">Assign Task</button>
        </div>
    </div>

    <!-- Modal for Assign Task -->
    <div class="modal" id="taskModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeTaskModal()">&times;</span>
            <h2 class="modal-header">Assign Task to Cleaner</h2>
            <form class="task-form" id="assignTaskForm">
                <label for="taskTitle">Task Title:</label>
                <input type="text" id="taskTitle" name="taskTitle" required>

                <label for="taskLocation">Location:</label>
                <input type="text" id="taskLocation" name="taskLocation" required>

                <label for="taskDateTime">Date & Time:</label>
                <input type="datetime-local" id="taskDateTime" name="taskDateTime" required>

                <label for="taskInstructions">Special Instructions:</label>
                <textarea id="taskInstructions" name="taskInstructions"></textarea>

                <button type="submit" class="assign-button">Assign Task</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cleanerCards = document.querySelectorAll('.cleaner-card');
        const modal = document.getElementById('cleanerModal');
        const taskModal = document.getElementById('taskModal');

        cleanerCards.forEach(card => {
            card.addEventListener('click', function() {
                const cleanerName = card.querySelector('.cleaner-name').textContent;
                const cleanerPhone = card.getAttribute('data-phone');
                const cleanerBuilding = card.getAttribute('data-building');
                const cleanerStatus = card.querySelector('.cleaner-status').textContent;
                const imgSrc = card.querySelector('.profile-pic').src;

                document.getElementById('modalCleanerName').textContent = cleanerName;
                document.getElementById('modalCleanerPhoneLink').textContent = cleanerPhone;
                document.getElementById('modalCleanerPhoneLink').href = `tel:${cleanerPhone}`;
                document.getElementById('modalCleanerBuilding').textContent = cleanerBuilding;
                document.getElementById('modalCleanerStatus').textContent = cleanerStatus;
                document.getElementById('modalProfilePic').src = imgSrc;

                modal.style.display = 'flex';
            });
        });

        document.querySelector('.assign-button').addEventListener('click', function() {
            modal.style.display = 'none';
            taskModal.style.display = 'flex';
        });

        function closeModal() {
            modal.style.display = 'none';
        }

        function closeTaskModal() {
            taskModal.style.display = 'none';
        }

        document.querySelector('.modal-close').addEventListener('click', closeModal);

        // Sort Functionality
        document.getElementById('sort-status').addEventListener('change', function() {
            const selectedStatus = this.value;
            cleanerCards.forEach(card => {
                const status = card.getAttribute('data-status');
                card.style.display = selectedStatus === 'all' || status === selectedStatus ? 'flex' : 'none';
            });
        });
    });

    function openTaskModal() {
        document.getElementById('cleanerModal').style.display = 'none';
        document.getElementById('taskModal').style.display = 'flex';
    }
</script>
</x-app-layout>
