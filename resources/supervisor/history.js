
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'toastr/build/toastr.min.css';
import './history.css'; 


document.addEventListener('DOMContentLoaded', function() {
    /**
     * Function to Toggle Details Visibility
     * @param {HTMLElement} button - The "View Details" button that was clicked.
     */
    function toggleDetails(button) {
        const id = button.getAttribute('data-id');
        const details = document.getElementById(`details-${id}`);
    
        if (details) {
            const isActive = details.classList.toggle('active');
            button.setAttribute('aria-expanded', isActive);
        } else {
            console.error(`Details div with ID details-${id} not found.`);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    

    /**
     * Function to Toggle Active/Inactive Button States
     * @param {HTMLElement} activeBtn - The button that should be active.
     * @param {Array<HTMLElement>} otherButtons - The buttons that should be inactive.
     */
    function toggleActive(activeBtn, otherButtons) {
        activeBtn.classList.remove('inactive');
        activeBtn.classList.add('active');
    
        otherButtons.forEach(button => {
            button.classList.remove('active');
            button.classList.add('inactive');
        });
    }

    /**
     * Function to Show Notifications
     * @param {string} message - The notification message to display.
     */
    function showNotification(message) {
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
    
        if (notification && notificationMessage) {
            notificationMessage.textContent = message;
            notification.classList.remove('hidden');
            notification.classList.add('visible');
    
            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('visible');
                notification.classList.add('hidden');
            }, 3000);
        }
    }

    /**
     * Function to Handle "Load More" Button Clicks
     * @param {HTMLElement} button - The "Load More" button that was clicked.
     */
    function handleLoadMore(button) {
        const status = button.getAttribute('data-status');
        const filter = button.getAttribute('data-filter');
        const page = button.getAttribute('data-page');
    
        console.log(`Loading more complaints: status=${status}, filter=${filter}, page=${page}`);
    
        // Show notification
        showNotification('Loading more complaints...');
    
        // Disable the button to prevent multiple clicks
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    
        // Fetch the next set of complaints via AJAX
        fetch(`${window.location.pathname}?status=${status}&filter=${filter}&page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('AJAX Response Status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received Data:', data);
            if (data.complaints && data.complaints.length > 0) {
                // Determine the target section based on status
                const targetSection = status === 'ongoing' ? '#ongoingOlder' : '#completedOlder';
                const container = document.querySelector(targetSection);
    
                // Get the complaint card template
                const template = document.getElementById('complaint-card-template');
    
                data.complaints.forEach(complaint => {
                    // Clone the template
                    const clone = template.content.cloneNode(true);
    
                    // Populate the clone with complaint data
                    const card = clone.querySelector('.card');
                    card.setAttribute('data-date', complaint.assigned_date);
    
                    // Update Card Header
                    const header = clone.querySelector('.card-header h3');
                    header.textContent = complaint.comp_location || 'N/A';
    
                    // Update Status Badge
                    const statusBadge = clone.querySelector('.status');
                    if (complaint.comp_status === 'ongoing') {
                        statusBadge.classList.remove('badge-completed');
                        statusBadge.classList.add('badge-ongoing');
                        statusBadge.innerHTML = `<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Ongoing`;
                    } else {
                        statusBadge.classList.remove('badge-ongoing');
                        statusBadge.classList.add('badge-completed');
                        statusBadge.innerHTML = `<i class="fas fa-check-circle" aria-hidden="true"></i> Completed`;
                    }
    
                    // Update Description
                    const description = clone.querySelector('.description');
                    description.textContent = complaint.comp_desc;
    
                    // Update "View Details" Button
                    const detailsButton = clone.querySelector('.btn-details');
                    detailsButton.setAttribute('data-id', complaint.id);
                    detailsButton.setAttribute('aria-controls', `details-${complaint.id}`);
    
                    // Update Details Section
                    const details = clone.querySelector('.toggle-content');
                    details.setAttribute('id', `details-${complaint.id}`);
    
                    const detailItems = details.querySelectorAll('.detail-item span');
                    detailItems[0].textContent = complaint.officer ? complaint.officer.name : 'Unknown Officer';
                    detailItems[1].textContent = complaint.comp_date ? new Date(complaint.comp_date).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A';
                    detailItems[2].textContent = complaint.assigned_date ? new Date(complaint.assigned_date).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A';
    
                    // Assigned Cleaners
                    const cleanersList = details.querySelector('.detail-item:nth-child(4) span');
                    if (complaint.cleaners && complaint.cleaners.length > 0) {
                        let cleanersHTML = '<ul class="cleaner-list">';
                        complaint.cleaners.forEach(cleaner => {
                            cleanersHTML += `<li>${cleaner.cleaner_name}`;
                            if (cleaner.cleaner_phoneNo) {
                                cleanersHTML += ` - <a href="tel:${cleaner.cleaner_phoneNo}" class="phone-link" aria-label="Call ${cleaner.cleaner_name}"><i class="fas fa-phone-alt" aria-hidden="true"></i> ${cleaner.cleaner_phoneNo}</a>`;
                            } else {
                                cleanersHTML += ` - N/A`;
                            }
                            cleanersHTML += `</li>`;
                        });
                        cleanersHTML += '</ul>';
                        cleanersList.innerHTML = cleanersHTML;
                    } else {
                        cleanersList.textContent = 'No cleaners assigned.';
                    }
    
                    // Assigned By
                    detailItems[4].textContent = complaint.supervisor ? complaint.supervisor.name : 'Unknown Officer';
    
                    // Add 'fade-in' class to trigger CSS transition
                    card.classList.add('fade-in');
    
                    // Append the cloned card before the "Load More" button
                    container.insertBefore(clone, container.querySelector('.load-more-container'));
                });
    
                // Update the "Load More" button
                if (data.hasMore) {
                    button.setAttribute('data-page', parseInt(page) + 1);
                    button.disabled = false;
                    button.innerHTML = 'Load More';
                } else {
                    // No more pages to load; remove the button
                    button.parentElement.remove();
                }
            } else {
                // No complaints returned; remove the "Load More" button
                button.parentElement.remove();
            }
        })
        .catch(error => {
            console.error('Error loading more complaints:', error);
            alert('An error occurred while loading more complaints. Please try again.');
            button.disabled = false;
            button.innerHTML = 'Load More';
        });
    }

    /**
     * Function to Handle Search Filtering
     * @param {string} searchTerm - The term to search for within complaint descriptions.
     */
    function handleSearch(searchTerm) {
        const allCards = document.querySelectorAll('.card'); 
        let anyVisible = false;
    
        allCards.forEach(card => {
            const description = card.querySelector('.description').textContent.toLowerCase() || '';
            const matches = description.includes(searchTerm);
            card.style.display = matches ? 'block' : 'none';
            if (matches) anyVisible = true;
        });
    
        // Handle empty state messages if no cards are visible
        toggleEmptyStates();
    }

    /**
     * Function to Handle Date Filtering
     * @param {string} selectedDate - The date to filter complaints by.
     */
    function handleDateFilter(selectedDate) {
        const allCards = document.querySelectorAll('.card'); // Select all cards in Completed and Ongoing sections
        let anyVisible = false;
    
        allCards.forEach(card => {
            const date = card.getAttribute('data-date') || '';
            if (selectedDate) {
                const matches = (date === selectedDate);
                card.style.display = matches ? 'block' : 'none';
                if (matches) anyVisible = true;
            } else {
                // If no date is selected, show all cards
                card.style.display = 'block';
            }
        });
    
        // Handle empty state messages if no cards are visible
        toggleEmptyStates();
    }

    /**
     * Function to Toggle Empty State Messages
     */
    function toggleEmptyStates() {
        // Determine visibility of Completed and Ongoing sections
        const completedVisible = Array.from(document.querySelectorAll('#completedToday .card, #completedThisWeek .card, #completedOlder .card'))
            .some(card => card.style.display === 'block');
        const ongoingVisible = Array.from(document.querySelectorAll('#ongoingToday .card, #ongoingThisWeek .card, #ongoingOlder .card'))
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

    /**
     * Function to Handle Button Group Filtering (Today, This Week, Older)
     * @param {NodeList} filterButtons - The set of filter buttons.
     * @param {NodeList} categories - The set of category containers.
     * @param {string} status - The complaint status (e.g., 'ongoing', 'completed').
     */
    function handleSubTabFilter(filterButtons, categories, status) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.add('inactive');
                });
    
                // Add active class to the clicked button
                this.classList.remove('inactive');
                this.classList.add('active');
    
                // Get the filter type
                const filter = this.getAttribute('data-filter');
    
                // Show/hide categories based on filter
                categories.forEach(category => {
                    if (filter === 'today' && category.id === `${status}Today`) {
                        category.style.display = 'block';
                        category.classList.add('active');
                    } else if (filter === 'thisWeek' && category.id === `${status}ThisWeek`) {
                        category.style.display = 'block';
                        category.classList.add('active');
                    } else if (filter === 'older' && category.id === `${status}Older`) {
                        category.style.display = 'block';
                        category.classList.add('active');
                    } else {
                        category.style.display = 'none';
                        category.classList.remove('active');
                    }
                });
    
                // After changing filter, check for empty states
                toggleEmptyStates();
            });
        });
    }

    /**
     * Function to Initialize Event Listeners
     */
    function initializeEventListeners() {
        // Button Group Functionality for Completed Complaints
        const showTodayBtn = document.getElementById('showTodayBtn');
        const showThisWeekBtn = document.getElementById('showThisWeekBtn');
        const showOlderBtn = document.getElementById('showOlderBtn');
    
        if (showTodayBtn && showThisWeekBtn && showOlderBtn) {
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
        }

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                handleSearch(searchTerm);
            });
        }

        // Date Filter Functionality
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', function () {
                const selectedDate = this.value;
                handleDateFilter(selectedDate);
            });
        }

        // Initialize Sub-Tab Filters for Ongoing and Completed Complaints
        const ongoingFilterButtons = document.querySelectorAll('#ongoing .btn-group button');
        const completedFilterButtons = document.querySelectorAll('#completed .btn-group button');
    
        const ongoingCategories = document.querySelectorAll('#ongoing .time-category');
        const completedCategories = document.querySelectorAll('#completed .time-category');
    
        handleSubTabFilter(ongoingFilterButtons, ongoingCategories, 'ongoing');
        handleSubTabFilter(completedFilterButtons, completedCategories, 'completed');
    }

    /**
     * Function to Initialize Main Tab Switching
     */
    function initializeMainTabs() {
        const mainTabs = document.querySelectorAll('.tab-navigation button');
        const tabContent = document.getElementById('tab-content');
    
        mainTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all main tabs
                mainTabs.forEach(t => {
                    t.classList.remove('active');
                    t.classList.add('inactive');
                });
    
                // Add active class to the clicked main tab
                this.classList.remove('inactive');
                this.classList.add('active');
    
                // Get the target tab
                const targetTab = this.getAttribute('data-tab');
    
                // Show the target tab content and hide others
                const panes = tabContent.querySelectorAll('.tab-pane');
                panes.forEach(pane => {
                    if (pane.id === targetTab) {
                        pane.style.display = 'block';
                        pane.classList.add('active');
                    } else {
                        pane.style.display = 'none';
                        pane.classList.remove('active');
                    }
                });

                // After switching tabs, check for empty states
                toggleEmptyStates();
            });
        });
    }

    /**
     * Function to Initialize All Event Listeners
     */
    function initializeAll() {
        initializeMainTabs();
        initializeEventListeners();
    }

    // Initialize everything
    initializeAll();

    /**
     * Event Delegation for "Load More" and "View Details" Buttons
     */
    document.addEventListener('click', function(e) {
        // Handle "Load More" Button Click
        const loadMoreButton = e.target.closest('.btn-load-more');
        if (loadMoreButton) {
            handleLoadMore(loadMoreButton);
            return; // Exit to prevent multiple handlers
        }

        // Handle "View Details" Button Click
        const detailsButton = e.target.closest('.btn-details');
        if (detailsButton) {
            toggleDetails(detailsButton);
        }
    });
});
