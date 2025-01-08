<!-- resources/views/partials/navigation.blade.php -->

<!-- Navbar Component -->
<nav x-data="notificationComponent()" class="bg-white/80 backdrop-blur-md fixed w-full z-10 transition duration-300 shadow-lg">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left Section: Logo and Links -->
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <a href="{{ route('supervisor.dashboard') }}" class="flex items-center">
                    <img src="{{ asset('/images/logo.png') }}" alt="Logo" class="h-10 w-auto" />
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex space-x-8">
                    <x-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.dashboard') ? 'bg-gray-200' : '' }}">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.cleaners') ? 'bg-gray-200' : '' }}">
                        {{ __('Cleaners') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.complaints.index') ? 'bg-gray-200' : '' }}">
                        {{ __('Complaints') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.history') ? 'bg-gray-200' : '' }}">
                        {{ __('History') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Section: Notifications & User Settings -->
            <div class="hidden sm:flex items-center space-x-6">
                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="notificationOpen = !notificationOpen" class="relative text-black hover:text-gray-700 transition duration-300 focus:outline-none">
                        <i class="fa fa-bell text-xl"></i>
                        <!-- Display Unread Notification Count -->
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white text-xs flex items-center justify-center rounded-full" x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen" @click.away="notificationOpen = false" class="absolute right-0 mt-3 w-96 bg-white rounded-xl shadow-lg z-50 overflow-hidden transition-all duration-300" x-cloak>
                        <!-- Dropdown Header with "Mark All as Read" Button -->
                        <div class="py-3 px-4 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                            
                        </div>
                        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                            <!-- Loop Through Unread Notifications -->
                            <template x-if="notifications.length > 0">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <a 
                                        :href="notification.data.complaint_id ? `/supervisor/complaints/${notification.data.complaint_id}` : '#'" 
                                        @click.prevent="notification.data.complaint_id ? markAsRead(notification.id) : alert('Invalid Complaint ID')" 
                                        class="block px-5 py-4 hover:bg-gray-50 transition flex items-start no-underline notification-item"
                                        :class="{ 'bg-blue-50': !notification.read_at, 'bg-white': notification.read_at }">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="bg-blue-100 text-blue-600 rounded-full h-10 w-10 flex items-center justify-center shadow-inner">
                                                <i class="fa fa-exclamation-circle text-lg"></i>
                                            </div>
                                        </div>
                                        <!-- Notification Content -->
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-medium text-gray-800 notification-title" x-text="notification.data.title ?? 'New Notification'"></p>
                                            <p class="text-sm text-gray-500 notification-subtitle" x-text="notification.data.message ?? ''"></p>
                                            <p class="text-xs text-gray-400 notification-time" x-text="timeSince(notification.created_at)"></p>
                                        </div>
                                    </a>
                                </template>
                            </template>
                            <!-- No Unread Notifications Message -->
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-5 text-sm text-gray-500 text-center">No new notifications</div>
                                
                            </template>
                        </div>
                        <!-- "View All Notifications" Link -->
                        <div class="py-2 text-center">
                            <a href="{{ route('supervisor.notifications.index') }}" class="text-sm text-blue-500 hover:underline">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-black hover:text-gray-700 focus:outline-none transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="rounded-lg shadow-lg overflow-hidden bg-white">
                            <x-dropdown-link :href="route('supervisor.profile.edit')" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="open = !open" class="sm:hidden text-black hover:text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>


<script>
    function notificationComponent() {
        return {
            open: false,
            notificationOpen: false,
            notifications: @json($unreadNotifications), // Use only unread notifications
            get unreadCount() {
                return this.notifications.length;
            },
            // Format the time to a relative format
            timeSince(date) {
                let seconds = Math.floor((new Date() - new Date(date)) / 1000);
                let interval = Math.floor(seconds / 31536000);
                if (interval >= 1) return interval + ' year' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) return interval + ' month' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) return interval + ' day' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) return interval + ' hour' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 60);
                if (interval >= 1) return interval + ' minute' + (interval > 1 ? 's' : '') + ' ago';
                return 'just now';
            },
            // Mark a single notification as read and redirect
            markAsRead(notificationId) {
                let notification = this.notifications.find(n => n.id === notificationId);
                if (notification && notification.data.complaint_id) {
                    // Redirect to the complaint detail page
                    window.location.href = `/supervisor/complaints/${notification.data.complaint_id}`;
                } else {
                    alert('Invalid Complaint ID');
                    return;
                }

                // Optionally, mark the notification as read via AJAX
                fetch(`/supervisor/notifications/read/${notificationId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        // Remove the notification from the list
                        this.notifications = this.notifications.filter(n => n.id !== notificationId);
                    } else {
                        console.error('Failed to mark notification as read:', response.status);
                    }
                })
                .catch(error => console.error('Error in markAsRead:', error));
            },
            // Mark all notifications as read
            markAllAsRead() {
                fetch(`/supervisor/notifications/mark-all-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        // Clear all notifications from the list
                        this.notifications = [];
                        this.notificationOpen = false;
                    } else {
                        console.error('Failed to mark all notifications as read:', response.status);
                    }
                })
                .catch(error => console.error('Error in markAllAsRead:', error));
            },
        };
    }
</script>

<!-- Include Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Styles and Fonts -->
<style>
    /* Notification Styles */
    .notification-item:hover {
        background-color: #f9f9f9;
    }

    .bg-blue-50 {
        background-color: #ebf8ff;
    }

    .bg-blue-100 {
        background-color: #e0f2fe;
    }

    .bg-blue-100:hover {
        background-color: #bae6fd;
    }

    .notification-time {
        color: #9ca3af;
    }

    .notification-title {
        color: #1f2937;
    }

    .notification-subtitle {
        color: #6b7280;
    }

</style>

<!-- Font Awesome (for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
