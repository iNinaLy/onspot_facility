import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './cleaner.css';

document.addEventListener('DOMContentLoaded', () => {
    /* --------------------------
       TABS
    ---------------------------*/
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            tabButtons.forEach((b) => b.classList.remove('active'));
            tabPanels.forEach((p) => p.classList.remove('active'));

            btn.classList.add('active');
            const targetId = btn.getAttribute('data-tab');
            document.getElementById(targetId).classList.add('active');
        });
    });

    /* --------------------------
       MODAL
    ---------------------------*/
    const modal = document.getElementById('cleanerModal');
    const closeButton = modal.querySelector('.close-button');

    // Profile / placeholder
    const modalProfilePic = document.getElementById('modal-profile-pic');
    const modalNoImagePlaceholder = document.getElementById('modal-no-image-placeholder');

    // Other fields
    const modalName = document.getElementById('modal-cleaner-name');
    const modalUsername = document.getElementById('modal-cleaner-username');
    const modalUsernameDetail = document.getElementById('modal-cleaner-username-detail');
    const statusContainer = document.getElementById('modal-cleaner-status-container');
    const modalPhoneLink = document.getElementById('modal-cleaner-phoneNo');
    const modalBuilding = document.getElementById('modal-cleaner-building');

    // Complaints
    const complaintSpinner = document.getElementById('complaint-spinner');
    const modalComplaints = document.getElementById('modal-cleaner-complaints');
    const complaintMessage = document.getElementById('complaint-message');

    // "View Details" Buttons
    document.querySelectorAll('.view-details-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            // Gather data from data-* attributes
            const name = btn.getAttribute('data-name');
            const profile = btn.getAttribute('data-profile');
            const phoneNo = btn.getAttribute('data-phoneNo');
            const username = btn.getAttribute('data-username');
            const status = btn.getAttribute('data-status');
            const building = btn.getAttribute('data-building');
            const complaints = JSON.parse(btn.getAttribute('data-complaints') || '[]');

            // Reset everything
            modalProfilePic.style.display = 'none';
            modalNoImagePlaceholder.style.display = 'none';
            complaintSpinner.style.display = 'block';
            modalComplaints.style.display = 'none';
            complaintMessage.style.display = 'none';
            modalComplaints.innerHTML = '';
            statusContainer.innerHTML = ''; // Clear previous status

            // Populate basic info
            modalName.textContent = name;
            modalUsername.textContent = '@' + username;
            modalUsernameDetail.textContent = username;

            // Create and populate status element
            let statusElement = document.createElement('span');
            statusElement.classList.add('modal-cleaner-status');

            if (status.toLowerCase() === 'available') {
                statusElement.classList.add('status-available');
                statusElement.textContent = 'Available';
            } else if (status.toLowerCase() === 'unavailable') {
                statusElement.classList.add('status-unavailable');
                statusElement.textContent = 'Unavailable';
            } else {
                statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            }
            statusContainer.appendChild(statusElement);

            modalPhoneLink.textContent = phoneNo;
            modalPhoneLink.href = 'tel:' + phoneNo;
            modalBuilding.textContent = building;

            // Show image or fallback
            if (profile && profile.length > 0) {
                modalProfilePic.src = profile;
                modalProfilePic.style.display = 'block';
            } else {
                modalNoImagePlaceholder.style.display = 'flex';
            }

            // Show modal
            modal.style.display = 'block';

            // Simulate loading or real AJAX
            setTimeout(() => {
                complaintSpinner.style.display = 'none';
                modalComplaints.style.display = 'block';

                if (complaints.length > 0) {
                    complaints.forEach((c) => {
                        const li = document.createElement('li');
                        li.textContent = c.desc + ' ';

                        // Status span for complaints
                        const statusSpan = document.createElement('span');
                        statusSpan.classList.add('complaint-status');
                        if (c.status.toLowerCase() === 'ongoing') {
                            statusSpan.innerHTML = `
                                <i class="fas fa-spinner fa-spin" style="margin-right: 4px;"></i>
                                ${c.status.charAt(0).toUpperCase() + c.status.slice(1)}
                            `;
                            statusSpan.classList.add('ongoing');
                        } else if (c.status.toLowerCase() === 'completed') {
                            statusSpan.textContent = c.status.charAt(0).toUpperCase() + c.status.slice(1);
                            statusSpan.classList.add('completed');
                        } else {
                            statusSpan.textContent = c.status.charAt(0).toUpperCase() + c.status.slice(1);
                        }
                        li.appendChild(statusSpan);
                        modalComplaints.appendChild(li);
                    });

                    if (complaints.some(comp => comp.status.toLowerCase() === 'ongoing')) {
                        complaintMessage.style.display = 'block';
                    }
                } else {
                    modalComplaints.innerHTML = '<li>No ongoing complaints assigned.</li>';
                }
            }, 500);
        });
    });

    // Close modal on (x)
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Close modal on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
        }
    });
});
