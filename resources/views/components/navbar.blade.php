<nav x-data="{ open: false, notificationOpen: false, notifications: @js($unreadNotifications) }" 
    class="bg-white/90 backdrop-blur-md fixed w-full z-10 transition-shadow duration-300 shadow-md">
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
                    <x-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" 
                        class="text-gray-900 font-semibold transition hover:text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 {{ request()->routeIs('supervisor.dashboard') ? 'bg-gray-200' : '' }}">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" 
                        class="text-gray-900 font-semibold transition hover:text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 {{ request()->routeIs('supervisor.cleaners') ? 'bg-gray-200' : '' }}">
                        {{ __('Cleaners') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" 
                        class="text-gray-900 font-semibold transition hover:text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 {{ request()->routeIs('supervisor.complaints.index') ? 'bg-gray-200' : '' }}">
                        {{ __('Complaints') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" 
                        class="text-gray-900 font-semibold transition hover:text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 {{ request()->routeIs('supervisor.history') ? 'bg-gray-200' : '' }}">
                        {{ __('History') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Section: Notifications & User Settings -->
            <div class="hidden sm:flex items-center space-x-6">
                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="notificationOpen = !notificationOpen" 
                        class="relative text-gray-900 hover:text-gray-700 transition duration-300 focus:outline-none">
                        <i class="fa fa-bell text-xl"></i>
                        <template x-if="notifications.length > 0">
                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></span>
                        </template>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen" 
                        @click.away="notificationOpen = false" 
                        class="absolute right-0 mt-3 w-96 bg-white rounded-lg shadow-lg z-50 overflow-hidden transform transition-all duration-300 origin-top-right scale-95"
                        x-cloak>
                        <div class="py-4 px-5 bg-gray-100 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Notifications</h3>
                        </div>
                        <div class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
                            <template x-if="notifications.length > 0">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <a @click.prevent="redirectAndMarkAsRead(notification)" 
                                        class="block px-5 py-4 hover:bg-gray-100 transition flex items-center">
                                        <div class="flex-shrink-0 bg-blue-200 text-blue-800 rounded-full h-10 w-10 flex items-center justify-center shadow-inner">
                                            <i class="fa fa-exclamation-circle"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                New Complaint Received
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                Made by: <span class="font-medium" x-text="notification.data.officer_name ?? 'Unknown Officer'"></span>
                                            </p>
                                            <p class="text-xs text-gray-500" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                        </div>
                                    </a>
                                </template>
                            </template>
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-5 text-sm text-gray-500 text-center">No new notifications</div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button @click="open = !open" 
                        class="flex items-center text-gray-900 hover:text-gray-700 focus:outline-none transition">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="ml-2 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path fill-rule="evenodd" 
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" 
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="open" 
                        @click.away="open = false" 
                        class="absolute right-0 mt-3 w-56 bg-white rounded-lg shadow-lg z-50 overflow-hidden transform transition-all duration-300 origin-top-right scale-95"
                        x-cloak>
                        <div class="py-4 px-5 bg-gray-100 border-b border-gray-200">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-700">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <a href="{{ route('supervisor.profile.edit') }}" 
                                class="block px-4 py-3 text-sm text-gray-900 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                    class="w-full text-left block px-4 py-3 text-sm text-gray-900 hover:bg-gray-100">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function redirectAndMarkAsRead(notification) {
        fetch(`/supervisor/notifications/read/${notification.id}`, { method: 'GET' })
            .then(response => {
                if (response.ok) {
                    window.location.href = `/supervisor/complaints/${notification.data.complaint_id}`;
                }
            });
    }
</script>


<style>
    /* Redesigned Styles */
    .notification-title {
        font-weight: 600;
        color: #1f2937;
    }

    .notification-subtitle {
        color: #6b7280;
    }

    .notification-time {
        color: #9ca3af;
    }

    .notification-item:hover {
        background-color: #f3f4f6;
    }

    .notification-bell:hover {
        color: #1f2937;
    }

    @media (prefers-color-scheme: dark) {
    .dark\:text-gray-100 {
        --tw-text-opacity: 1;
        color: rgb(24 25 27);
    }
</style>


<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
