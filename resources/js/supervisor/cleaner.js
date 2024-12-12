import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './cleaner.css';

// Ensure the DOM is fully loaded before executing scripts
document.addEventListener('DOMContentLoaded', () => {
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
        const profilePic = button.getAttribute('data-profile') || '/images/default-placeholder.png';
        const phoneNo = button.getAttribute('data-phoneNo') || 'N/A';
        const username = button.getAttribute('data-username') || 'N/A';
        const building = button.getAttribute('data-building') || 'N/A';
        const status = button.getAttribute('data-status') || 'N/A';
        const complaints = JSON.parse(button.getAttribute('data-complaints')) || [];

        // Populate modal elements
        modalProfilePic.src = profilePic.startsWith('data:image') ? profilePic : '/images/default-placeholder.png';
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
});
