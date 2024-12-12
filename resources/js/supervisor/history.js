// resources/supervisor/history.js

// Importing CSS files (handled via Blade's @push('styles'))
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import '../css/history.css'; 

// Toggle Details Function
function toggleDetails(id) {
    const details = document.getElementById(`details-${id}`);
    details.classList.toggle('active');
}

// Button Group Functionality and Search/Filter Handlers
document.addEventListener('DOMContentLoaded', () => {
    const showTodayBtn = document.getElementById('showTodayBtn');
    const showThisWeekBtn = document.getElementById('showThisWeekBtn');
    const showOlderBtn = document.getElementById('showOlderBtn');

    // Button Click Event Listeners
    showTodayBtn.addEventListener('click', function() {
        document.getElementById('completedToday').style.display = 'block';
        document.getElementById('completedThisWeek').style.display = 'none';
        document.getElementById('completedOlder').style.display = 'none';

        toggleActive(this, [showThisWeekBtn, showOlderBtn]);
    });

    showThisWeekBtn.addEventListener('click', function() {
        document.getElementById('completedToday').style.display = 'none';
        document.getElementById('completedThisWeek').style.display = 'block';
        document.getElementById('completedOlder').style.display = 'none';

        toggleActive(this, [showTodayBtn, showOlderBtn]);
    });

    showOlderBtn.addEventListener('click', function() {
        document.getElementById('completedToday').style.display = 'none';
        document.getElementById('completedThisWeek').style.display = 'none';
        document.getElementById('completedOlder').style.display = 'block';

        toggleActive(this, [showTodayBtn, showThisWeekBtn]);
    });

    // Function to toggle active/inactive button states
    function toggleActive(activeBtn, otherButtons) {
        activeBtn.classList.remove('btn-secondary', 'inactive');
        activeBtn.classList.add('btn-primary', 'active');

        otherButtons.forEach(button => {
            button.classList.remove('btn-primary', 'active');
            button.classList.add('btn-secondary', 'inactive');
        });
    }

    // Search Functionality Applied to All Complaints
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const allCards = document.querySelectorAll('.card'); // Select all cards in Completed and Ongoing sections

        allCards.forEach(card => {
            const description = card.dataset.description || ''; // Use the dataset description
            card.style.display = description.includes(searchTerm) ? 'block' : 'none';
        });

        // Handle empty state messages if no cards are visible
        handleEmptyStates();
    });

    // Date Filter Functionality Applied to All Complaints
    const dateFilter = document.getElementById('dateFilter');
    dateFilter.addEventListener('change', function () {
        const selectedDate = this.value;
        const allCards = document.querySelectorAll('.card'); // Select all cards in Completed and Ongoing sections

        allCards.forEach(card => {
            const date = card.dataset.date || '';
            if (selectedDate) {
                card.style.display = (date === selectedDate) ? 'block' : 'none';
            } else {
                // If no date is selected, show all cards
                card.style.display = 'block';
            }
        });

        // Handle empty state messages if no cards are visible
        handleEmptyStates();
    });

    // Function to handle empty state messages
    function handleEmptyStates() {
        // Check visibility for Completed Complaints
        const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
            .some(card => card.style.display === 'block');

        // Check visibility for Ongoing Complaints
        const ongoingVisible = Array.from(document.querySelectorAll('#ongoingComplaints .card'))
            .some(card => card.style.display === 'block');

        // Show or hide empty state messages
        const emptyStateCompleted = document.querySelector('.empty-state-completed');
        const emptyStateOngoing = document.querySelector('.empty-state-ongoing');

        if (emptyStateCompleted) {
            emptyStateCompleted.style.display = completedVisible ? 'none' : 'flex';
        }

        if (emptyStateOngoing) {
            emptyStateOngoing.style.display = ongoingVisible ? 'none' : 'block';
        }
    }

    // Initialize empty state visibility on page load
    handleEmptyStates();
});
