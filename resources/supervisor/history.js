import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './history.css'; 

function toggleDetails(id) {
    const details = document.getElementById(`details-${id}`);
    details.classList.toggle('active');
}

// Button Group Functionality
const showTodayBtn = document.getElementById('showTodayBtn');
const showThisWeekBtn = document.getElementById('showThisWeekBtn');
const showOlderBtn = document.getElementById('showOlderBtn');

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
    activeBtn.classList.remove('inactive');
    activeBtn.classList.add('active');

    otherButtons.forEach(button => {
        button.classList.remove('active');
        button.classList.add('inactive');
    });
}

// Search Functionality Applied to All Complaints
document.getElementById('searchInput').addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    const allCards = document.querySelectorAll('.card'); 

    allCards.forEach(card => {
        const description = card.dataset.description || ''; // Use the dataset description
        card.style.display = description.includes(searchTerm) ? 'block' : 'none';
    });

    // Handle empty state messages if no cards are visible
    const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
        .some(card => card.style.display === 'block');
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
});

// Date Filter Functionality Applied to All Complaints
document.getElementById('dateFilter').addEventListener('change', function () {
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
    const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
        .some(card => card.style.display === 'block');
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
});