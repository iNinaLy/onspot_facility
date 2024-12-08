@extends('layouts.app')

@section('content')
<style>
    /* Base Styles */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #F7F7F7;
        color: #4A4A4A;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 4rem; /* Added margin-top */
        margin-bottom: 1.5rem;
    }

    .notification-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2e5675;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
        margin-bottom: 1rem;
    }

    .tab {
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        border: none;
        background: none;
        outline: none;
        transition: background-color 0.3s ease;
        font-size: 1rem;
        font-weight: 500;
        color: #555;
    }

    .tab:hover {
        background-color: #f0f0f0;
    }

    .tab.active {
        border-bottom: 4px solid #2e5675;
        color: #2e5675;
    }

    .notification-list {
        max-height: 600px;
        overflow-y: auto;
    }

    .notification-card {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .notification-card:hover {
        background-color: #f0f4f8;
        transform: translateY(-2px);
    }

    .notification-card.unread {
        border-left: 5px solid #2e5675;
        background-color: #eef2f5;
    }

    .notification-card .content {
        flex-grow: 1;
        margin-right: 1.5rem;
    }

    .notification-card .content p {
        margin: 0.25rem 0;
    }

    .notification-card .content .time {
        color: #7A7A7A;
        font-size: 0.875rem;
    }

    .notification-card .actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Button Styles */

    /* "View" Button */
    .btn-view {
        display: flex;
        align-items: center;
        background-color: #2e5675;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem; /* Reduced padding */
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 0.8rem; /* Reduced font size */
        font-weight: 500;
    }

    .btn-view:hover {
        background-color: #23465e;
        transform: translateY(-2px);
    }

    .btn-view:active {
        transform: translateY(0);
    }

    .btn-view:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
        transform: none;
    }

    /* "Mark All as Read" Button */
    .btn-mark-read {
        display: flex;
        align-items: center;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem; /* Reduced padding */
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 0.8rem; /* Reduced font size */
        font-weight: 500;
        margin-right: 0.5rem; /* Adjusted margin */
    }

    .btn-mark-read:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    .btn-mark-read:active {
        transform: translateY(0);
    }

    .btn-mark-read:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
        transform: none;
    }

    /* "Delete" Button */
    .btn-delete {
        display: flex;
        align-items: center;
        background-color: #d9534f;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem; /* Reduced padding */
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 0.8rem; /* Reduced font size */
        font-weight: 500;
    }

    .btn-delete:hover {
        background-color: #c9302c;
        transform: translateY(-2px);
    }

    .btn-delete:active {
        transform: translateY(0);
    }

    /* "Delete All" Button */
    .btn-delete-all {
        display: flex;
        align-items: center;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem; /* Reduced padding */
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 0.8rem; /* Reduced font size */
        font-weight: 500;
        margin-right: 0.5rem; /* Adjusted margin */
    }

    .btn-delete-all:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    .btn-delete-all:active {
        transform: translateY(0);
    }

    .btn-delete-all:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
        transform: none;
    }

    /* Icon Styles */
    .icon {
        width: 1rem; /* Reduced icon size */
        height: 1rem; /* Reduced icon size */
        margin-right: 0.3rem; /* Reduced margin */
        flex-shrink: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .notification-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .notification-card .actions {
            margin-top: 0.75rem;
            width: 100%;
            justify-content: flex-start;
        }

        .btn-view, .btn-mark-read, .btn-delete, .btn-delete-all {
            width: auto; /* Allow buttons to adjust width */
            justify-content: center;
        }

        .icon {
            margin-right: 0.2rem; /* Adjusted margin */
        }

        .notification-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .notification-header h2 {
            margin-bottom: 0.5rem;
        }
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            --tw-bg-opacity: 1;
            background-color: rgb(234 234 234);
        }
    }

    @media (prefers-color-scheme: dark) {
        .dark\:border-gray-600 {
            --tw-border-opacity: 0.5;
            border-color: rgb(219 221 224);
        }
    }
</style>

<!-- Include SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Optionally include Heroicons via CDN if not already included -->
<script src="https://unpkg.com/heroicons@2.0.13/dist/outline/solid.js"></script>

<div class="container mx-auto px-4 py-8" x-data="notificationComponent()">
    <!-- Notifications Header -->
    <div class="notification-header">
        <h2>Notifications</h2>
        <div>
            <!-- "Delete All" Button (Visible Only in "All Notifications" Tab) -->
            <button 
                type="button" 
                class="btn-delete-all" 
                @click="deleteAllNotifications()" 
                x-show="activeTab === 'all' && notifications.data.length > 0"
                title="Delete all notifications"
            >
                <!-- Trash Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete All
            </button>
            <!-- "Mark All as Read" Button (Visible Only in "New Notifications" Tab) -->
            <button 
                type="button" 
                class="btn-mark-read" 
                @click="markAllAsRead()" 
                x-show="activeTab === 'new' && unreadCount > 0"
                title="Mark all unread notifications as read"
            >
                <!-- Check Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Mark All as Read
            </button>
        </div>
    </div>

    <!-- Tabs for Switching Between New and All Notifications -->
    <div class="tabs" role="tablist">
        <button 
            type="button"
            class="tab" 
            :class="{'active': activeTab === 'new'}" 
            @click="activeTab = 'new'" 
            role="tab" 
            :aria-selected="activeTab === 'new'" 
            tabindex="0"
            @keydown.enter.prevent="activeTab = 'new'"
        >
            New Notifications (<span x-text="unreadCount"></span>)
        </button>
        <button 
            type="button"
            class="tab" 
            :class="{'active': activeTab === 'all'}" 
            @click="activeTab = 'all'" 
            role="tab" 
            :aria-selected="activeTab === 'all'" 
            tabindex="0"
            @keydown.enter.prevent="activeTab = 'all'"
        >
            All Notifications
        </button>
    </div>

    <!-- Notification List -->
    <div class="notification-list">
        <!-- New Notifications Tab -->
        <template x-if="activeTab === 'new'">
            <div>
                <template x-if="unreadNotifications.length === 0">
                    <p class="text-center text-gray-500">No new notifications.</p>
                </template>

                <template x-for="notification in unreadNotifications" :key="notification.id">
                    <div :class="{'notification-card unread': !notification.read_at, 'notification-card': notification.read_at}">
                        <div class="content">
                            <p class="font-semibold">
                                🚨 <span x-text="notification.data.title || 'Notification'"></span>
                            </p>
                            <p x-text="notification.data.message || 'No details available.'"></p>
                            <p class="time" x-text="timeSince(notification.created_at)"></p>
                        </div>
                        <div class="actions">
                            <button 
                                type="button" 
                                class="btn-view" 
                                @click="viewNotification(notification.id, notification.data.complaint_id, notification.read_at)"
                                :disabled="!notification.data.complaint_id"
                                :title="!notification.data.complaint_id ? 'No associated complaint' : 'View Notification'"
                            >
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View
                            </button>
                            <button 
                                type="button" 
                                class="btn-delete" 
                                @click="deleteNotification(notification.id)"
                                :title="'Delete Notification'"
                            >
                                <!-- Trash Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- All Notifications Tab -->
        <template x-if="activeTab === 'all'">
            <div>
                <template x-if="notifications.data.length === 0">
                    <p class="text-center text-gray-500">No notifications found.</p>
                </template>

                <template x-for="notification in notifications.data" :key="notification.id">
                    <div :class="{'notification-card unread': !notification.read_at, 'notification-card': notification.read_at}">
                        <div class="content">
                            <p class="font-semibold">
                                🚨 <span x-text="notification.data.title || 'Notification'"></span>
                            </p>
                            <p x-text="notification.data.message || 'No details available.'"></p>
                            <p class="time" x-text="timeSince(notification.created_at)"></p>
                        </div>
                        <div class="actions">
                            <!-- "View" Button -->
                            <button 
                                type="button" 
                                class="btn-view" 
                                @click="viewNotification(notification.id, notification.data.complaint_id, notification.read_at)"
                                :disabled="!notification.data.complaint_id"
                                :title="!notification.data.complaint_id ? 'No associated complaint' : 'View Notification'"
                            >
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View
                            </button>
                            <!-- "Delete" Button -->
                            <button 
                                type="button" 
                                class="btn-delete" 
                                @click="deleteNotification(notification.id)"
                                :title="'Delete Notification'"
                            >
                                <!-- Trash Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </template>
    </div>

    <!-- Include Alpine.js and SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function notificationComponent() {
            return {
                activeTab: 'new', // 'new' or 'all'
                notifications: @json($notifications), // Paginated notifications for 'all' tab
                unreadNotifications: @json($unreadNotifications), // Unread notifications for 'new' tab
                unreadCount: @json($unreadNotificationsCount), // Count of unread notifications

                /**
                 * Formats the notification time to a relative format.
                 * @param {string} date - The notification creation date.
                 * @returns {string} - Formatted time string.
                 */
                timeSince(date) {
                    let seconds = Math.floor((new Date() - new Date(date)) / 1000);
                    let interval = Math.floor(seconds / 31536000);
                    if (interval >= 1) return `${interval} year${interval > 1 ? 's' : ''} ago`;
                    interval = Math.floor(seconds / 2592000);
                    if (interval >= 1) return `${interval} month${interval > 1 ? 's' : ''} ago`;
                    interval = Math.floor(seconds / 86400);
                    if (interval >= 1) return `${interval} day${interval > 1 ? 's' : ''} ago`;
                    interval = Math.floor(seconds / 3600);
                    if (interval >= 1) return `${interval} hour${interval > 1 ? 's' : ''} ago`;
                    interval = Math.floor(seconds / 60);
                    if (interval >= 1) return `${interval} minute${interval > 1 ? 's' : ''} ago`;
                    return 'just now';
                },

                /**
                 * Marks a single notification as read (if unread) and redirects to the complaint detail page.
                 * @param {number} notificationId - The ID of the notification.
                 * @param {number|null} complaintId - The ID of the associated complaint.
                 * @param {string|null} readAt - The read_at timestamp of the notification.
                 */
                async viewNotification(notificationId, complaintId, readAt) {
                    console.log('View button clicked:', notificationId, complaintId, readAt);

                    // Check if the complaintId exists
                    if (!complaintId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No associated complaint for this notification.',
                        });
                        return;
                    }

                    try {
                        if (!readAt) { // If the notification is unread
                            // Show a loading indicator
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Marking notification as read.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Send POST request to mark the notification as read
                            const response = await fetch(`/supervisor/notifications/read/${notificationId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Failed to mark notification as read.');
                            }

                            const data = await response.json();

                            if (data.status === 'success') {
                                // Update the notification's read_at status in the 'all' tab
                                this.notifications.data = this.notifications.data.map(n => {
                                    if (n.id === notificationId) {
                                        return { ...n, read_at: new Date().toISOString() };
                                    }
                                    return n;
                                });

                                // Remove the notification from the 'new' tab
                                this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notificationId);
                                this.unreadCount--;

                                // Close the loading indicator
                                Swal.close();
                            } else {
                                throw new Error(data.message || 'Failed to mark notification as read.');
                            }
                        }

                        // Show a redirecting loading indicator
                        Swal.fire({
                            title: 'Redirecting...',
                            text: 'Opening the complaint details.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Perform the redirect
                        window.location.href = `/supervisor/complaints/${complaintId}`;
                    } catch (error) {
                        console.error('Error in viewNotification:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'An error occurred while processing your request.',
                        });
                    }
                },

                /**
                 * Deletes a single notification after confirmation.
                 * @param {number} notificationId - The ID of the notification to delete.
                 */
                async deleteNotification(notificationId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will delete the notification permanently.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d9534f',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                // Show a loading indicator
                                Swal.fire({
                                    title: 'Deleting...',
                                    text: 'Please wait while the notification is being deleted.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Send DELETE request to delete the notification
                                const response = await fetch(`/supervisor/notifications/${notificationId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                    },
                                });

                                if (!response.ok) {
                                    const errorData = await response.json();
                                    throw new Error(errorData.message || 'Failed to delete notification.');
                                }

                                const data = await response.json();

                                if (data.status === 'success') {
                                    // Remove the notification from the 'all' tab
                                    this.notifications.data = this.notifications.data.filter(n => n.id !== notificationId);

                                    // If it's in the 'new' tab, also remove it and update the count
                                    this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notificationId);
                                    // Assuming 'read_at' is present in the notification data
                                    const isUnread = !data.read_at; // Adjust based on actual response
                                    if (isUnread) {
                                        this.unreadCount--;
                                    }

                                    // Close the loading indicator and show success message
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Notification has been deleted.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    throw new Error(data.message || 'Failed to delete notification.');
                                }
                            } catch (error) {
                                console.error('Error in deleteNotification:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'An error occurred while deleting the notification.',
                                });
                            }
                        }
                    });
                },

                /**
                 * Deletes all notifications after confirmation.
                 */
                async deleteAllNotifications() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will delete all notifications permanently.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d9534f',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete all!'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                // Show a loading indicator
                                Swal.fire({
                                    title: 'Deleting...',
                                    text: 'Please wait while all notifications are being deleted.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Send DELETE request to delete all notifications
                                const response = await fetch(`/supervisor/notifications/clear-all`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                    },
                                });

                                if (!response.ok) {
                                    const errorData = await response.json();
                                    throw new Error(errorData.message || 'Failed to delete all notifications.');
                                }

                                const data = await response.json();

                                if (data.status === 'success') {
                                    // Clear all notifications from both tabs
                                    this.notifications.data = [];
                                    this.unreadNotifications = [];
                                    this.unreadCount = 0;

                                    // Close the loading indicator and show success message
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'All notifications have been deleted.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    throw new Error(data.message || 'Failed to delete all notifications.');
                                }
                            } catch (error) {
                                console.error('Error in deleteAllNotifications:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'An error occurred while deleting notifications.',
                                });
                            }
                        }
                    });
                },

                /**
                 * Marks all unread notifications as read.
                 */
                async markAllAsRead() {
                    if (this.unreadCount === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Notifications',
                            text: 'There are no unread notifications to mark as read.',
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This will mark all unread notifications as read.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#2e5675',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, mark all as read!'
                    });

                    if (result.isConfirmed) {
                        try {
                            // Show a loading indicator
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Marking all notifications as read.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Send POST request to mark all as read
                            const response = await fetch(`/supervisor/notifications/mark-all-as-read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Failed to mark all notifications as read.');
                            }

                            const data = await response.json();

                            if (data.status === 'success') {
                                // Clear all unread notifications
                                this.unreadNotifications = [];
                                this.unreadCount = 0;

                                // Update all notifications in the 'all' tab as read
                                this.notifications.data = this.notifications.data.map(n => ({
                                    ...n,
                                    read_at: n.read_at || new Date().toISOString()
                                }));

                                // Close the loading indicator
                                Swal.close();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'All unread notifications have been marked as read.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                throw new Error(data.message || 'Failed to mark all notifications as read.');
                            }
                        } catch (error) {
                            console.error('Error in markAllAsRead:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'An error occurred while processing your request.',
                            });
                        }
                    }
                },
            };
        }
    </script>
@endsection
