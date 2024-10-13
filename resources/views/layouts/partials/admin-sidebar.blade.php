<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@heroicons/react@1.0.0/outline/heroicons.min.js"></script>
</head>
<body class="bg-gray-100">

<!-- Sidebar Container -->
<div id="sidebar-container" class="fixed inset-y-0 left-0 w-64 md:w-72 bg-white shadow-lg transition-transform transform -translate-x-full md:translate-x-0 z-50 rounded-r-3xl">
    <div class="relative h-full flex flex-col">
        <!-- Logo and Profile Section -->
        <div class="flex items-center justify-between py-6 px-6 border-b border-gray-200">
            <img id="profile_pic" src="https://via.placeholder.com/40" alt="User Profile Picture" class="h-12 rounded-full shadow-md">
            <div class="text-sm md:text-base font-semibold text-gray-800">
                <span id="user-name">{{ Auth::user()->name }}</span>
                <div class="text-xs text-gray-500" id="user-username">Username: {{ Auth::user()->username }}</div>
                <div class="text-xs text-gray-500" id="user-email">Email: {{ Auth::user()->email }}</div>
                <div class="text-xs text-gray-500" id="user-role">Role: {{ Auth::user()->role }}</div>
            </div>
            <button id="toggle-button" class="p-2 focus:outline-none md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>


        <!-- Sidebar Content -->
        <div class="flex-1 px-6 py-4 space-y-2">
            <!-- Manage Dropdown -->
            <div>
                <button class="flex items-center justify-between w-full p-3 text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-300" onclick="toggleSubmenu()">
                    <span class="flex items-center">
                        <!-- Updated Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-4 text-gray-600 hover:text-gray-800 transition-colors duration-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5.5 3a1.5 1.5 0 000 3h9a1.5 1.5 0 000-3h-9zM3 8.5A1.5 1.5 0 014.5 7h11a1.5 1.5 0 010 3h-11A1.5 1.5 0 013 8.5zM6.5 13a1.5 1.5 0 000 3h7a1.5 1.5 0 000-3h-7z" />
                        </svg>
                        <span class="text-sm md:text-base">Manage</span>
                    </span>
                    <span id="arrow-icon" class="transition-transform transform rotate-0 duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </button>

                <div id="manageSubmenu" class="hidden pl-6 space-y-2">
                    <a href="#" class="block p-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-300">Officer</a>
                    <a href="#" class="block p-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-300">Supervisor</a>
                    <a href="#" class="block p-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-300">Cleaner</a>
                </div>
            </div>

            <!-- Complaints Link -->
            <a href="#" class="flex items-center p-3 text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-300">
                <!-- Updated Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-4 text-gray-600 hover:text-gray-800 transition-colors duration-300" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927a1 1 0 011.902 0l1.618 4.956a1 1 0 00.95.69h5.18a1 1 0 010 2h-5.18a1 1 0 00-.95.69l-1.618 4.956a1 1 0 01-1.902 0L7.431 11.2a1 1 0 00-.95-.69H1.25a1 1 0 110-2h5.231a1 1 0 00.95-.69L9.049 2.927z" />
                </svg>
                <span class="text-sm md:text-base">Complaints</span>
            </a>
        </div>

        <!-- Account Section -->
        <div class="px-6 py-4 border-t border-gray-200 space-y-2">
            <!-- Profile Link -->
            <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-900 flex items-center p-3 hover:bg-gray-100 rounded-lg transition-colors duration-300">
                <!-- Profile Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-4 text-gray-600 hover:text-gray-800 transition-colors duration-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a4 4 0 100 8 4 4 0 000-8zM4 10a6 6 0 1112 0 6 6 0 01-12 0zm6 8a8 8 0 110-16 8 8 0 010 16z" clip-rule="evenodd" />
                </svg>
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();" class="text-gray-900 flex items-center p-3 hover:bg-gray-100 rounded-lg transition-colors duration-300">
                    <!-- Logout Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-4 text-red-500 hover:text-red-700 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m0 0a3 3 0 010-6h6m0 6V4" />
                    </svg>
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</div>

<style>
    #sidebar-container {
        height: 100vh;
        background-color: #ffffff;
        border-top-right-radius: 32px;
        border-bottom-right-radius: 32px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }
    .transition-transform {
        transition: all 0.3s ease-in-out;
    }
    a:hover {
        background-color: #f3f4f6;
    }
</style>

<script>
    function toggleSubmenu() {
        const submenu = document.getElementById('manageSubmenu');
        const arrowIcon = document.getElementById('arrow-icon');
        submenu.classList.toggle('hidden');
        arrowIcon.classList.toggle('rotate-90');
    }

    document.getElementById('toggle-button').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar-container');
        sidebar.classList.toggle('-translate-x-full');
    });
</script>

</body>
</html>
