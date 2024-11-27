<nav x-data="{ open: false, notificationOpen: false }" 
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
                        class="text-black transition-colors hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('supervisor.dashboard') ? 'shadow-md' : '' }}">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" 
                        class="text-black transition-colors hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('supervisor.cleaners') ? 'shadow-md' : '' }}">
                        {{ __('Cleaners') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" 
                        class="text-black transition-colors hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('supervisor.complaints.index') ? 'shadow-md' : '' }}">
                        {{ __('Complaints') }}
                    </x-nav-link>
                    <x-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" 
                        class="text-black transition-colors hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('supervisor.history') ? 'shadow-md' : '' }}">
                        {{ __('History') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Section: Notifications & User Settings -->
            <div class="hidden sm:flex items-center space-x-6">
                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="notificationOpen = !notificationOpen" 
                        class="relative text-black hover:text-gray-900 transition duration-300 focus:outline-none">
                        <i class="fa fa-bell text-xl"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-600 rounded-full"></span>
                        @endif
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen" 
                        @click.away="notificationOpen = false" 
                        class="absolute right-0 mt-3 w-64 bg-white rounded-lg shadow-lg z-50 overflow-hidden transition-all duration-300"
                        x-cloak>
                        <div class="divide-y divide-gray-100">
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                @foreach(auth()->user()->unreadNotifications as $notification)
                                    <div class="px-4 py-3 hover:bg-gray-50">
                                        <p class="text-sm font-medium text-black">{{ $notification->data['comp_desc'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-3 text-sm text-gray-500">No new notifications</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-black hover:text-gray-900 focus:outline-none transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
            <button @click="open = !open" class="sm:hidden text-black hover:text-gray-900 focus:outline-none">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="space-y-1 pt-2 pb-3">
            <x-responsive-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" class="text-black">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.cleaners')" :active="request()->routeIs('supervisor.cleaners')" class="text-black">
                {{ __('Cleaners') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.complaints.index')" :active="request()->routeIs('supervisor.complaints.index')" class="text-black">
                {{ __('Complaints') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('supervisor.history')" :active="request()->routeIs('supervisor.history')" class="text-black">
                {{ __('History') }}
            </x-responsive-nav-link>
        </div>
    </div>
</nav>

<style>
    @media (prefers-color-scheme: dark) {
        .dark\:text-gray-100 {
            --tw-text-opacity: 1;
            color: rgb(75 78 86);
        }
    }

    @media (prefers-color-scheme: dark) {
        .dark\:text-gray-300 {
            --tw-text-opacity: 1;
            color: rgb(15 16 17);
        }
    }
</style>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">