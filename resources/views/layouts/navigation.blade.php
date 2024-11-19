<nav x-data="{ open: false, notificationOpen: false, notifications: @js($unreadNotifications) }" 
    class="bg-white/80 backdrop-blur-md fixed w-full z-10 transition duration-300 shadow-lg">
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
                        class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.dashboard') ? 'bg-gray-200' : '' }}">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" 
                        class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.cleaners') ? 'bg-gray-200' : '' }}">
                        {{ __('Cleaners') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" 
                        class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.complaints.index') ? 'bg-gray-200' : '' }}">
                        {{ __('Complaints') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" 
                        class="text-black font-semibold transition hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 {{ request()->routeIs('supervisor.history') ? 'bg-gray-200' : '' }}">
                        {{ __('History') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Section: Notifications & User Settings -->
            <div class="hidden sm:flex items-center space-x-6">
              
            <!-- Notification Bell -->
                <div class="relative">
                    <button @click="notificationOpen = !notificationOpen" 
                        class="relative text-black hover:text-gray-700 transition duration-300 focus:outline-none">
                        <i class="fa fa-bell text-xl"></i>
                        <template x-if="notifications.some(notification => !notification.read_at)">
                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-600 rounded-full"></span>
                        </template>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen" 
                        @click.away="notificationOpen = false" 
                        class="absolute right-0 mt-3 w-96 bg-white rounded-xl shadow-lg z-50 overflow-hidden transition-all duration-300"
                        x-cloak>
                        <div class="py-3 px-4 bg-gray-100 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                            <template x-if="notifications.length > 0">
                                <template x-for="notification in notifications" :key="notification.id">
                                <a 
                                    :href="notification.data.comp_id ? `{{ route('supervisor.complaints.show', ['id' => '__ID__']) }}`.replace('__ID__', notification.data.comp_id) : '#'" 
                                    @click.prevent="notification.data.comp_id ? markAsRead(notification.id, notification.data.comp_id) : alert('Invalid Complaint ID')" 
                                    class="block px-5 py-4 hover:bg-gray-50 transition flex items-center no-underline notification-item"
                                    :class="{ 'bg-blue-100': !notification.read_at }">
                                    <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center shadow-inner">
                                        <i class="fa fa-exclamation-circle text-lg"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-800 notification-title">
                                            New Complaint Received
                                        </p>
                                        <p class="text-sm text-gray-500 notification-subtitle">
                                            Made by: <span class="font-semibold" x-text="notification.data.officer_name ?? 'Unknown Officer'"></span>
                                        </p>
                                        <p class="text-xs text-gray-400 notification-time" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                    </div>
                                    <i class="fa fa-chevron-right text-gray-400"></i>
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
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-black hover:text-gray-700 focus:outline-none transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path fill-rule="evenodd" 
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" 
                                    clip-rule="evenodd" />
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
                                <x-dropdown-link :href="route('logout')" 
                                    onclick="event.preventDefault(); this.closest('form').submit();" 
                                    class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>

<script>

    function markAsRead(notificationId, compId) {
        fetch(`/supervisor/notifications/read/${notificationId}`, { method: 'GET' })
            .then(response => {
                if (response.ok) {
                    // Redirect to the complaint details page after marking as read
                    window.location.href = `/supervisor/complaints/${compId}`;
                } else {
                    console.error('Failed to mark notification as read:', response.status);
                }
            })
            .catch(error => console.error('Error in markAsRead:', error));
    }


</script>

<style>
    @media (prefers-color-scheme: dark) {
        .dark\:text-gray-100 {
            --tw-text-opacity: 1;
            color: rgb(55 57 60);
        }
    }
    .notification-card {
        border-radius: 12px;
        background: linear-gradient(90deg, #ffffff 0%, #f9f9f9 100%);
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .notification-item:hover {
        background-color: #f9f9f9;
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

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
