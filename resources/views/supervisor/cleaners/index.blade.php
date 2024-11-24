@extends('layouts.app')

@section('title', 'Cleaners')

@push('styles')
<style>
    :root {
        --primary-color: #2E5675;
        --secondary-color: #4caf50;
        --available-color: #2196f3;
        --unavailable-color: #f44336;
        --modal-bg: rgba(0, 0, 0, 0.5);
        --modal-content-bg: #ffffff;
        --modal-border-radius: 15px;
        --close-button-color: #aaa;
        --close-button-hover-color: #000;
        --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        --transition-duration: 0.3s;
        --badge-font-size: 0.85rem;
        --badge-padding: 0.4rem 0.8rem;
        --tooltip-bg-color: var(--primary-color);
        --tooltip-text-color: #fff;
    }

    /* Global Styles */
    body {
        font-family: var(--font-family);
        background-color: #f4f7fa;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* Container */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* Header with Filters */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 3rem;
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
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        width: 200px;
        transition: border-color var(--transition-duration);
    }

    .filter-container select:focus,
    .filter-container input:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    /* Cleaner Overview Section */
    .cleaner-overview {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .overview-box {
        flex: 1;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(5px);
        border-radius: 15px;
        padding: 15px 20px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform var(--transition-duration), box-shadow var(--transition-duration);
    }

    .overview-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .overview-number {
        font-size: 1.6rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
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

    /* Cleaner List Styling */
    .cleaner-list {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cleaner-list thead {
        background-color: var(--primary-color);
        color: #fff;
    }

    .cleaner-list th,
    .cleaner-list td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.95rem;
        vertical-align: middle;
    }

    .cleaner-list th {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cleaner-row {
        cursor: pointer;
        transition: background-color var(--transition-duration);
        display: table-row;
    }

    .cleaner-row:hover {
        background-color: rgba(46, 86, 117, 0.05);
    }

    .cleaner-profile-pic {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
        border: 2px solid var(--available-color);
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .cleaner-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: block;
    }

    .cleaner-status {
        font-size: var(--badge-font-size);
        font-weight: 500;
        padding: var(--badge-padding);
        border-radius: 9999px;
        display: inline-block;
        color: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color var(--transition-duration), box-shadow var(--transition-duration);
    }

    .status-available {
        background: linear-gradient(135deg, var(--available-color) 0%, #6dd5ed 100%);
    }

    .status-unavailable {
        background: linear-gradient(135deg, var(--unavailable-color) 0%, #ff7e5f 100%);
    }

    /* Ongoing Badge for Complaint Statuses */
    .badge-ongoing {
        background-color: #d1ecf1; /* Light Blue */
        color: #0c5460; /* Dark Blue Text */
        border: 1px solid #bee5eb;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Enhance status badge hover effect */
    .cleaner-status:hover,
    .badge-ongoing:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* No Cleaners Found Message */
    #no-cleaners-message {
        display: none;
    }

    #no-cleaners-message td {
        text-align: center;
        padding: 1rem;
        font-size: 1rem;
        color: #555;
    }

    /* View Details Button */
    .view-details-btn {
        padding: 0.6rem 1.2rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: background-color var(--transition-duration);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .view-details-btn:hover {
        background-color: #1f3c52;
    }

    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; 
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: var(--modal-bg); /* Semi-transparent background */
        backdrop-filter: blur(8px); /* Blurred background */
        padding-top: 60px;
        transition: opacity var(--transition-duration);
    }

    .modal-content {
        background-color: var(--modal-content-bg);
        margin: auto;
        padding: 2rem;
        border: none;
        border-radius: var(--modal-border-radius);
        width: 90%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
        animation-name: slideIn;
        animation-duration: 0.3s;
    }

    @keyframes slideIn {
        from {transform: translateY(-50px); opacity: 0;}
        to {transform: translateY(0); opacity: 1;}
    }

    .close-button {
        color: var(--close-button-color);
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color var(--transition-duration);
    }

    .close-button:hover,
    .close-button:focus {
        color: var(--close-button-hover-color);
        text-decoration: none;
    }

    .modal-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .modal-profile-pic {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
        border: 4px solid var(--available-color);
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .modal-cleaner-name {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 0.3rem;
        color: var(--primary-color);
    }

    .modal-cleaner-username {
        font-size: 1.1rem;
        color: #6b7280;
        margin-bottom: 0.8rem;
    }

    .modal-cleaner-status {
        font-size: var(--badge-font-size);
        font-weight: 500;
        padding: var(--badge-padding);
        border-radius: 9999px;
        display: inline-block;
        color: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color var(--transition-duration), box-shadow var(--transition-duration);
    }

    .modal-cleaner-status.status-available {
        background: linear-gradient(135deg, var(--available-color) 0%, #6dd5ed 100%);
    }

    .modal-cleaner-status.status-unavailable {
        background: linear-gradient(135deg, var(--unavailable-color) 0%, #ff7e5f 100%);
    }

    .modal-cleaner-status.status-ongoing {
        background-color: #d1ecf1; /* Light Blue */
        color: #0c5460; /* Dark Blue Text */
        border: 1px solid #bee5eb;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Enhance modal status badge hover effect */
    .modal-cleaner-status:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .modal-cleaner-details {
        width: 100%;
        margin-top: 1rem;
        text-align: left;
    }

    .modal-cleaner-details p {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-cleaner-details strong {
        color: var(--primary-color);
    }

    /* Assigned Complaints Styling */
    .assigned-complaints {
        width: 100%;
        margin-top: 1.5rem;
        text-align: left;
    }

    .assigned-complaints h3 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .assigned-complaints ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .assigned-complaints li {
        background-color: #f9f9f9;
        padding: 0.7rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .assigned-complaints li .complaint-status {
        font-size: var(--badge-font-size);
        font-weight: 500;
        padding: var(--badge-padding);
        border-radius: 9999px;
        display: inline-block;
        color: #0c5460;
        margin-left: auto;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color var(--transition-duration), box-shadow var(--transition-duration);
    }

    

    .complaint-status.ongoing {
        background-color: #d1ecf1; /* Light Blue */
        color: #0c5460; /* Dark Blue Text */
        border: 1px solid #bee5eb;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Enhance complaint status badge hover effect */
    .complaint-status:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Tooltip Styling */
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 140px;
        background-color: var(--tooltip-bg-color);
        color: var(--tooltip-text-color);
        text-align: center;
        border-radius: 6px;
        padding: 6px 0;
        position: absolute;
        z-index: 1;
        bottom: 125%; /* Position above the icon */
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity var(--transition-duration);
        font-size: 0.85rem;
    }

    .tooltip:hover .tooltiptext,
    .tooltip:focus .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    /* Responsive Design */
    @media screen and (max-width: 768px) {
        .modal-content {
            width: 95%;
        }

        .assigned-complaints h3 {
            font-size: 1rem;
        }

        .cleaner-profile-pic {
            width: 50px;
            height: 50px;
        }

        .modal-profile-pic {
            width: 80px;
            height: 80px;
        }

        .modal-cleaner-name {
            font-size: 1.5rem;
        }

        .modal-cleaner-username {
            font-size: 1rem;
        }

        .cleaner-name {
            font-size: 0.95rem;
        }

        .cleaner-status {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        .complaint-status {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
    }

    /* Scrollbar Styling (optional for better aesthetics) */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary-color); 
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555; 
    }
</style>
@endpush

@section('content')
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
            <select id="sort-by" class="sort-select">
                <option value="default">Sort By</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="status_asc">Status (Available First)</option>
                <option value="status_desc">Status (Unavailable First)</option>
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

    <!-- Cleaner List -->
    <table class="cleaner-list" id="cleaner-list">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Building</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cleaners as $cleaner)
            <tr class="cleaner-row" 
                data-name="{{ strtolower($cleaner->cleaner_name) }}" 
                data-status="{{ strtolower($cleaner->status) }}" 
                data-id="{{ $cleaner->id }}"
                data-phone="{{ $cleaner->cleaner_phoneNo }}">
                <td>
                    @if($cleaner->profile_pic)
                        <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" alt="Cleaner Profile Picture" class="cleaner-profile-pic" loading="lazy">
                    @else
                        <img src="{{ asset('images/default-placeholder.png') }}" alt="Profile Picture" class="cleaner-profile-pic" loading="lazy">
                    @endif
                </td>
                <td>
                    <span class="cleaner-name">{{ $cleaner->cleaner_name }}</span>
                </td>
                <td>
                    {{ $cleaner->cleaner_phoneNo }}
                </td>
                <td>{{ $cleaner->building }}</td>
                <td>
                    @if($cleaner->status == 'available')
                        <span class="cleaner-status status-available">Available</span>
                    @elseif($cleaner->status == 'unavailable')
                        <span class="cleaner-status status-unavailable">Unavailable</span>
                    @endif
                </td>
                <td>
                    <button class="view-details-btn"
                            data-name="{{ $cleaner->cleaner_name }}"
                            data-profile="{{ $cleaner->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($cleaner->profile_pic) : asset('images/default-placeholder.png') }}"
                            data-phoneNo="{{ $cleaner->cleaner_phoneNo }}"
                            data-username="{{ $cleaner->cleaner_username }}"
                            data-building="{{ $cleaner->building }}"
                            data-status="{{ $cleaner->status }}"
                            data-complaints="{{ json_encode($cleaner->ongoingComplaints->map(function($complaint) { return ['desc' => $complaint->comp_desc, 'status' => $complaint->comp_status]; })) }}">
                        View Details
                    </button>
                </td>
            </tr>
            @endforeach

            <!-- No Cleaners Found Message -->
            <tr id="no-cleaners-message" style="display: none;">
                <td colspan="6">No cleaners found matching your criteria.</td>
            </tr>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $cleaners->links() }}
    </div>
</div>

<!-- Cleaner Details Modal -->
<div id="cleanerModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-cleaner-name" aria-describedby="modal-cleaner-details">
    <div class="modal-content">
        <span class="close-button" aria-label="Close Modal">&times;</span>
        <div class="modal-body">
            <img src="" alt="Cleaner Profile Picture" class="modal-profile-pic">
            <h2 class="modal-cleaner-name" id="modal-cleaner-name"></h2>
            <span class="modal-cleaner-username" id="modal-cleaner-username"></span>
            <p class="modal-cleaner-status"></p>
            <div class="modal-cleaner-details" id="modal-cleaner-details">
                <p>
                    <strong>Phone Number:</strong> 
                    <a href="#" class="modal-cleaner-phoneLink" target="_blank" rel="noopener noreferrer"></a>
                </p>
                <p>
                    <strong>Building:</strong> 
                    <span class="modal-cleaner-building"></span>
                </p>
            </div>
            <div class="assigned-complaints">
                <h3>Assigned Complaints:</h3>
                <ul id="modal-cleaner-complaints">
                    <!-- Assigned complaints will be injected here -->
                </ul>
                <p id="complaint-message" style="display: none; color: #555; font-size: 0.95rem;">
                    Cleaners still have ongoing tasks to be completed.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Font Awesome CDN for Spinner Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pYxNt+Dm+1NmiZZmUwyNq0B0Eyz4TRMXVjV7Z+QvNaw0lJZbdKU+RxmYpRKEtEjqNj+FwAB6S2gk7nVtK2yqdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>
    // Get modal elements
    const modal = document.getElementById('cleanerModal');
    const closeButton = document.querySelector('.close-button');

    // Get all view details buttons
    const viewDetailsButtons = document.querySelectorAll('.view-details-btn');

    // Elements inside the modal to populate
    const modalProfilePic = document.querySelector('.modal-profile-pic');
    const modalCleanerName = document.querySelector('.modal-cleaner-name');
    const modalCleanerUsername = document.getElementById('modal-cleaner-username');
    const modalCleanerStatus = document.querySelector('.modal-cleaner-status');
    const modalCleanerPhoneLink = document.querySelector('.modal-cleaner-phoneLink');
    const modalCleanerBuilding = document.querySelector('.modal-cleaner-building');
    const modalCleanerComplaints = document.getElementById('modal-cleaner-complaints');
    const complaintMessage = document.getElementById('complaint-message');

    // Function to open modal and populate data
    function openModal(button) {
        const name = button.getAttribute('data-name') || 'N/A';
        const profilePic = button.getAttribute('data-profile') || '{{ asset('images/default-placeholder.png') }}';
        const phoneNo = button.getAttribute('data-phoneNo') || 'N/A';
        const username = button.getAttribute('data-username') || 'N/A';
        const building = button.getAttribute('data-building') || 'N/A';
        const status = button.getAttribute('data-status') || 'N/A';
        const complaints = JSON.parse(button.getAttribute('data-complaints')) || [];

        // Populate modal elements
        modalProfilePic.src = profilePic.startsWith('data:image') ? profilePic : '{{ asset('images/default-placeholder.png') }}';
        modalProfilePic.alt = `${name}'s Profile Picture`;
        modalCleanerName.textContent = name;
        modalCleanerUsername.textContent = `@${username}`;
        modalCleanerBuilding.textContent = building;

        // Assign appropriate class and content based on cleaner status
        if(status.toLowerCase() === 'available'){
            modalCleanerStatus.className = `modal-cleaner-status status-available`;
            modalCleanerStatus.textContent = `Available`;
        }
        else if(status.toLowerCase() === 'unavailable'){
            modalCleanerStatus.className = `modal-cleaner-status status-unavailable`;
            modalCleanerStatus.textContent = `Unavailable`;
        }
        else{
            modalCleanerStatus.className = `modal-cleaner-status`;
            modalCleanerStatus.textContent = `${capitalizeFirstLetter(status)}`;
        }

        // Populate Phone Number as WhatsApp Link
        if(phoneNo && phoneNo !== 'N/A'){
            const sanitizedNumber = sanitizePhoneNumber(phoneNo);
            modalCleanerPhoneLink.textContent = phoneNo;
            modalCleanerPhoneLink.href = `https://wa.me/${sanitizedNumber}`;
            modalCleanerPhoneLink.style.pointerEvents = 'auto';
            modalCleanerPhoneLink.style.color = '#2E5675'; // Primary color
        } else {
            modalCleanerPhoneLink.textContent = 'N/A';
            modalCleanerPhoneLink.href = '#';
            modalCleanerPhoneLink.style.pointerEvents = 'none';
            modalCleanerPhoneLink.style.color = '#6b7280'; // Gray color
        }

        // Populate Assigned Complaints
        modalCleanerComplaints.innerHTML = ''; // Clear previous entries
        if (complaints.length > 0) {
            complaints.forEach(complaint => {
                const li = document.createElement('li');

                // Create complaint description
                const descSpan = document.createElement('span');
                descSpan.textContent = complaint.desc;

                // Create complaint status badge
                const statusSpan = document.createElement('span');
                statusSpan.classList.add('complaint-status');

                // Assign class based on complaint status
                switch(complaint.status.toLowerCase()) {
                    case 'pending':
                        statusSpan.classList.add('pending');
                        statusSpan.textContent = 'Pending';
                        break;
                    case 'in progress':
                        statusSpan.classList.add('in_progress');
                        statusSpan.textContent = 'In Progress';
                        break;
                    case 'resolved':
                        statusSpan.classList.add('resolved');
                        statusSpan.textContent = 'Resolved';
                        break;
                    case 'ongoing':
                        statusSpan.classList.add('ongoing');
                        statusSpan.innerHTML = `<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing`;
                        break;
                    default:
                        statusSpan.classList.add('pending');
                        statusSpan.textContent = 'Pending';
                }

                // Append to list item
                li.appendChild(descSpan);
                li.appendChild(statusSpan);
                modalCleanerComplaints.appendChild(li);
            });
            // Show message if there are ongoing tasks
            complaintMessage.style.display = 'block';
        } else {
            const li = document.createElement('li');
            li.textContent = 'No assigned complaints.';
            modalCleanerComplaints.appendChild(li);
            // Hide the additional message
            complaintMessage.style.display = 'none';
        }

        // Display the modal
        modal.style.display = 'block';

        // Trap focus within the modal
        trapFocus(modal);

        // Set focus to the modal for accessibility
        modal.setAttribute('tabindex', '-1');
        modal.focus();
    }

    // Helper function to capitalize first letter
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Function to sanitize phone number for WhatsApp links
    function sanitizePhoneNumber(phoneNo) {
        // Remove all non-digit characters
        return phoneNo.replace(/\D/g, '');
    }

    // Function to trap focus within the modal
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll('a[href], button:not([disabled]), textarea, input, select');
        const firstFocusableElement = focusableElements[0];  
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            const isTabPressed = (e.key === 'Tab' || e.keyCode === 9);

            if (!isTabPressed) { 
                return; 
            }

            if (e.shiftKey) /* shift + tab */ {
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else /* tab */ {
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        });
    }

    // Add click event to all view details buttons
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', () => {
            openModal(button);
        });
    });

    // Close modal when the close button is clicked
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal when user clicks outside the modal content
    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Close modal with Esc key
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            modal.style.display = 'none';
        }
    });

    // Search and Filter Functionality
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('sort-status');
    const sortByFilter = document.getElementById('sort-by');
    const cleanerList = document.getElementById('cleaner-list');
    const cleanerRows = document.querySelectorAll('.cleaner-row');
    const noCleanersMessage = document.getElementById('no-cleaners-message');

    function filterCleaners() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;

        let visibleCount = 0; // Counter for visible rows

        cleanerRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const status = row.getAttribute('data-status');

            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show or hide the "No Cleaners Found" message
        if (visibleCount === 0) {
            noCleanersMessage.style.display = 'table-row';
        } else {
            noCleanersMessage.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', filterCleaners);
    statusFilter.addEventListener('change', filterCleaners);

    // Sorting Functionality
    function sortCleaners() {
        const sortBy = sortByFilter.value;
        let rows = Array.from(cleanerRows);

        switch(sortBy) {
            case 'name_asc':
                rows.sort((a, b) => a.getAttribute('data-name').localeCompare(b.getAttribute('data-name')));
                break;
            case 'name_desc':
                rows.sort((a, b) => b.getAttribute('data-name').localeCompare(a.getAttribute('data-name')));
                break;
            case 'status_asc':
                rows.sort((a, b) => {
                    if (a.getAttribute('data-status') === b.getAttribute('data-status')) return 0;
                    return a.getAttribute('data-status') === 'available' ? -1 : 1;
                });
                break;
            case 'status_desc':
                rows.sort((a, b) => {
                    if (a.getAttribute('data-status') === b.getAttribute('data-status')) return 0;
                    return a.getAttribute('data-status') === 'unavailable' ? -1 : 1;
                });
                break;
            default:
                // Default sorting (e.g., by name ascending)
                rows.sort((a, b) => a.getAttribute('data-name').localeCompare(b.getAttribute('data-name')));
        }

        // Re-append sorted rows to the table body
        const tbody = cleanerList.querySelector('tbody');
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));

        // Re-append the "No Cleaners Found" message row at the end
        tbody.appendChild(noCleanersMessage);
    }

    sortByFilter.addEventListener('change', () => {
        sortCleaners();
        filterCleaners(); // Re-apply filters after sorting
    });

    // Initial filter check in case there are no cleaners on page load
    document.addEventListener('DOMContentLoaded', () => {
        filterCleaners();
    });
</script>
@endpush

<!-- Cleaner Details Modal -->
<div id="cleanerModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-cleaner-name" aria-describedby="modal-cleaner-details">
    <div class="modal-content">
        <span class="close-button" aria-label="Close Modal">&times;</span>
        <div class="modal-body">
            <img src="" alt="Cleaner Profile Picture" class="modal-profile-pic">
            <h2 class="modal-cleaner-name" id="modal-cleaner-name"></h2>
            <span class="modal-cleaner-username" id="modal-cleaner-username"></span>
            <p class="modal-cleaner-status"></p>
            <div class="modal-cleaner-details" id="modal-cleaner-details">
                <p>
                    <strong>Phone Number:</strong> 
                    <a href="#" class="modal-cleaner-phoneLink" target="_blank" rel="noopener noreferrer"></a>
                </p>
                <p>
                    <strong>Building:</strong> 
                    <span class="modal-cleaner-building"></span>
                </p>
            </div>
            <div class="assigned-complaints">
                <h3>Assigned Complaints:</h3>
                <ul id="modal-cleaner-complaints">
                    <!-- Assigned complaints will be injected here -->
                </ul>
                <p id="complaint-message" style="display: none; color: #555; font-size: 0.95rem;">
                    Cleaners still have ongoing tasks to be completed.
                </p>
            </div>
        </div>
    </div>
</div>
