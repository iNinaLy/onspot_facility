@extends('layouts.app')
@section('title', 'Notifications')

@push('styles')
    <!-- External Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://unpkg.com/heroicons@2.0.13/dist/outline/solid.js"></script>
@endpush

@section('content')
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                                @click="viewNotification(notification.id, notification.read_at)"
                                :disabled="!notification.data.complaint_id"
                                :title="!notification.data.complaint_id ? 'No associated complaint' : 'View Notification'"
                            >
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" 
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5
                                             c4.477 0 8.268 2.943 9.542 7
                                             -1.274 4.057-5.065 7-9.542 7
                                             -4.477 0-8.268-2.943-9.542-7z" />
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" 
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                             a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                             m1-10V4a1 1 0 00-1-1h-4
                                             a1 1 0 00-1 1v3M4 7h16" />
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
                            <button 
                                type="button" 
                                class="btn-view" 
                                @click="viewNotification(notification.id, notification.read_at)"
                                :disabled="!notification.data.complaint_id"
                                :title="!notification.data.complaint_id ? 'No associated complaint' : 'View Notification'"
                            >
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" 
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M2.458 12C3.732 7.943 7.523 5
                                             12 5c4.477 0 8.268 2.943 9.542 7
                                             -1.274 4.057-5.065 7-9.542 7
                                             -4.477 0-8.268-2.943-9.542-7z" />
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" 
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                             a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                             m1-10V4a1 1 0 00-1-1h-4
                                             a1 1 0 00-1 1v3M4 7h16" />
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
</div>

@push('scripts')
    @vite([
        'resources/supervisor/app.js',
        'resources/supervisor/dashboard.js',
        'resources/supervisor/complaint.js',
    ])
    <!-- Include Alpine.js and SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function notificationComponent() {
            return {
                activeTab: 'new', // 'new' or 'all'
                notifications: @json($notifications), // Paginated notifications for the 'all' tab
                unreadNotifications: @json($unreadNotifications), // Unread notifications for the 'new' tab
                unreadCount: @json($unreadNotificationsCount), // Count of unread notifications

                /**
                 * Formats the notification time to a relative format.
                 * @param {string} date - The notification creation date.
                 * @returns {string} - Formatted time string (e.g., "5 minutes ago").
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
                 * Marks a single notification as read (if unread), then redirects
                 * to a dedicated route where the server determines complaint status.
                 *
                 * @param {number} notificationId - The ID of the notification.
                 * @param {string|null} readAt - The read_at timestamp (null if unread).
                 */
                async viewNotification(notificationId, readAt) {
                    console.log('View button clicked:', { notificationId, readAt });

                    try {
                        // If the notification is unread, mark it as read first
                        if (!readAt) {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Marking notification as read.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

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
                                // Update 'all' tab
                                this.notifications.data = this.notifications.data.map(n => {
                                    if (n.id === notificationId) {
                                        return { ...n, read_at: new Date().toISOString() };
                                    }
                                    return n;
                                });

                                // Remove from 'new' tab
                                this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notificationId);
                                this.unreadCount--;

                                Swal.close();
                            } else {
                                throw new Error(data.message || 'Failed to mark notification as read.');
                            }
                        }

                        // Now let the server handle the redirect
                        Swal.fire({
                            title: 'Redirecting...',
                            text: 'Opening the complaint details.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // We always call the new redirect route
                        window.location.href = `/supervisor/notifications/redirect/${notificationId}`;
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
                                Swal.fire({
                                    title: 'Deleting...',
                                    text: 'Please wait while the notification is being deleted.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

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
                                    // Remove from 'all' tab
                                    this.notifications.data = this.notifications.data.filter(n => n.id !== notificationId);

                                    // If it's in 'new' tab, remove it too
                                    this.unreadNotifications = this.unreadNotifications.filter(n => n.id !== notificationId);

                                    // (Optionally decrement unreadCount if it was unread)
                                    // this.unreadCount--;

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
                                Swal.fire({
                                    title: 'Deleting...',
                                    text: 'Please wait while all notifications are being deleted.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

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
                                    // Clear both arrays & reset unread count
                                    this.notifications.data = [];
                                    this.unreadNotifications = [];
                                    this.unreadCount = 0;

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
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Marking all notifications as read.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

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

                                // Mark every notification in "all" as read
                                this.notifications.data = this.notifications.data.map(n => ({
                                    ...n,
                                    read_at: n.read_at || new Date().toISOString()
                                }));

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
@endpush
@endsection
