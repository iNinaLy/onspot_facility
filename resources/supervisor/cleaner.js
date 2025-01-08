// resources/supervisor/cleaner.js

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import '././cleaner.css'; 


//Search//


document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('DOMContentLoaded', () => {
        
    })

    const cleanerModalElement = document.getElementById('cleanerModal');
    const cleanerModal = new bootstrap.Modal(cleanerModalElement, { keyboard: true });

    // Helper to populate elements in the modal
    function populateElement(id, { text, src, href, styleDisplay, stylePointerEvents, styleColor }) {
        const el = document.getElementById(id);
        if (!el) return;
        if (text !== undefined) el.textContent = text;
        if (src !== undefined) el.src = src;
        if (href !== undefined) el.href = href;
        if (styleDisplay !== undefined) el.style.display = styleDisplay;
        if (stylePointerEvents !== undefined) el.style.pointerEvents = stylePointerEvents;
        if (styleColor !== undefined) el.style.color = styleColor;
        return el;
    }

    document.addEventListener('click', function(e) {
        const viewDetailsBtn = e.target.closest('.view-details-btn');
        if (viewDetailsBtn) {
            const name = viewDetailsBtn.getAttribute('data-name') || 'N/A';
            const profile = viewDetailsBtn.getAttribute('data-profile') || 'no_image';
            const phoneNo = viewDetailsBtn.getAttribute('data-phoneNo') || 'N/A';
            const username = viewDetailsBtn.getAttribute('data-username') || 'N/A';
            const building = viewDetailsBtn.getAttribute('data-building') || 'N/A';
            const status = viewDetailsBtn.getAttribute('data-status') || 'N/A';
            const complaints = JSON.parse(viewDetailsBtn.getAttribute('data-complaints')) || [];

            if (profile !== 'no_image') {
                populateElement('modal-profile-pic', { src: profile, styleDisplay: 'block' });
                populateElement('modal-no-image-placeholder', { styleDisplay: 'none' });
            } else {
                populateElement('modal-profile-pic', { styleDisplay: 'none' });
                populateElement('modal-no-image-placeholder', { styleDisplay: 'flex' });
            }

            populateElement('modal-cleaner-name', { text: name });
            populateElement('modal-cleaner-status', { text: status.charAt(0).toUpperCase() + status.slice(1) });
            document.getElementById('modal-cleaner-status').className = 'cleaner-status status-' + status.toLowerCase();

            populateElement('modal-cleaner-username', { text: username });

            if (phoneNo !== 'N/A') {
                populateElement('modal-cleaner-phoneNo', { 
                    text: phoneNo, 
                    href: `tel:${phoneNo}`, 
                    stylePointerEvents: 'auto', 
                    styleColor: '#0d6efd' 
                });
            } else {
                populateElement('modal-cleaner-phoneNo', { 
                    text: 'N/A', 
                    href: '#', 
                    stylePointerEvents: 'none', 
                    styleColor: 'gray' 
                });
            }

            populateElement('modal-cleaner-building', { text: building });

            const modalComplaints = document.getElementById('modal-cleaner-complaints');
            const modalComplaintMessage = document.getElementById('complaint-message');
            modalComplaints.innerHTML = '';

            if (complaints.length > 0) {
                complaints.forEach(complaint => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item');
                    li.textContent = complaint.desc + ' ';
                    const statusSpan = document.createElement('span');
                    statusSpan.classList.add('badge', getComplaintBadgeClass(complaint.status));
                    statusSpan.innerHTML = (complaint.status.toLowerCase() === 'ongoing') ?
                        `<i class="fas fa-spinner fa-spin"></i> ${complaint.status}` :
                        complaint.status;
                    li.appendChild(statusSpan);
                    modalComplaints.appendChild(li);
                });
                modalComplaintMessage.style.display = 'block';
            } else {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.textContent = 'No complaints assigned.';
                modalComplaints.appendChild(li);
                modalComplaintMessage.style.display = 'none';
            }

            cleanerModal.show();
        }
    });

    function getComplaintBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'ongoing': return 'bg-info text-dark';
            case 'completed': return 'bg-success';
            default: return 'bg-secondary';
        }
    }
});


