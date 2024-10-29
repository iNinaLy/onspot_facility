<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom scrollbar */
        #sidebar-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(100, 100, 100, 0.5) transparent;
        }
        #sidebar-container::-webkit-scrollbar {
            width: 8px;
        }
        #sidebar-container::-webkit-scrollbar-thumb {
            background-color: rgba(100, 100, 100, 0.5);
            border-radius: 8px;
        }

        /* Sidebar transition */
        .sidebar-enter {
            transform: translateX(-100%);
            opacity: 0;
        }
        .sidebar-enter-active {
            transform: translateX(0);
            opacity: 1;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        /* Blur background effect */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop-hidden {
            opacity: 0;
            pointer-events: none;
        }
        .sidebar-backdrop-visible {
            opacity: 1;
        }

        /* Active link styling */
        .active-link {
            background-color: #2e5675;
            color: #FFFFFF;
        }

        /* Sidebar background */
        #sidebar-container {
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
        }

        /* Animation for submenu arrow */
        .rotate-90 {
            transform: rotate(90deg);
        }

        /* Improved spacing */
        .menu-item {
            transition: background-color 0.3s ease;
        }
        .menu-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-100 relative">

    <!-- Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="sidebar-backdrop sidebar-backdrop-hidden"></div>

    <!-- Sidebar Container -->
    <div id="sidebar-container" class="fixed inset-y-0 left-0 w-64 md:w-72 bg-white shadow-lg transform -translate-x-full md:translate-x-0 z-50 rounded-r-3xl">
        <div class="relative h-full flex flex-col overflow-y-auto">

            <!-- Profile Section -->
            @php
                $adminUser = \App\Models\User::where('role', 'admin')->first();
            @endphp
            <div class="flex items-center justify-between py-4 px-4 border-b border-gray-200">
                @if($adminUser)
                    <div class="flex items-center">
                        <img src="{{ $adminUser->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($adminUser->profile_pic) : 'https://via.placeholder.com/40' }}" 
                             alt="Profile Picture" 
                             class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <div class="text-sm font-semibold">{{ $adminUser->name }}</div>
                            <div class="text-xs text-gray-500">{{ $adminUser->username }}</div>
                            <div class="text-xs text-gray-500">{{ $adminUser->email }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Admin user not found.</p>
                @endif
                <button id="toggle-button" class="p-2 focus:outline-none md:hidden" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 px-4 py-4 space-y-2">

                <!-- Home Link -->
                <a href="{{ route('admin.dashboard') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 20a1 1 0 01-1-1V11H6a1 1 0 01-1-1V8.414L10 3.586l5 4.828V10a1 1 0 01-1 1h-3v8a1 1 0 01-1 1z" />
                    </svg>
                    {{ __('Home') }}
                </a>

                <!-- Manage Dropdown -->
                <div>
                    <button class="menu-item w-full p-2 flex items-center justify-between rounded-lg" onclick="toggleSubmenu()" aria-expanded="false" aria-controls="manageSubmenu">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5.5 3a1.5 1.5 0 000 3h9a1.5 1.5 0 000-3h-9zM3 8.5A1.5 1.5 0 014.5 7h11a1.5 1.5 0 010 3h-11A1.5 1.5 0 013 8.5zM6.5 13a1.5 1.5 0 000 3h7a1.5 1.5 0 000-3h-7z" />
                            </svg>
                            <span>{{ __('Manage') }}</span>
                        </span>
                        <svg id="arrow-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Manage Submenu -->
                    <div id="manageSubmenu" class="hidden pl-4 space-y-2">
                        <a href="{{ route('admin.officers.index') }}" class="menu-item p-2 block rounded-lg">
                            {{ __('Officer') }}
                        </a>
                        <a href="{{ route('admin.supervisors.index') }}" class="menu-item p-2 block rounded-lg">
                            {{ __('Supervisor') }}
                        </a>
                        <a href="{{ route('admin.cleaners.index') }}" class="menu-item p-2 block rounded-lg">
                            {{ __('Cleaner') }}
                        </a>
                    </div>
                </div>

                <!-- New User Link -->
                <a href="{{ route('admin.users.create') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.users.create') ? 'active-link' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    {{ __('New User') }}
                </a>

                <!-- Complaints Link -->
                <a href="{{ route('admin.complaints.index') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.complaints.index') ? 'active-link' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927a1 1 0 011.902 0l1.618 4.956a1 1 0 00.95.69h5.18a1 1 0 010 2h-5.18a1 1 0 00-.95.69l-1.618 4.956a1 1 0 01-1.902 0L7.431 11.2a1 1 0 00-.95-.69H1.25a1 1 0 110-2h5.231a1 1 0 00.95-.69L9.049 2.927z" />
                    </svg>
                    {{ __('Complaints') }}
                </a>

            </div>

            <!-- Account Section -->
            <div class="px-4 py-4 border-t border-gray-200 space-y-2">
                <a href="{{ route('admin.profile.edit') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.profile.edit') ? 'active-link' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a4 4 0 100 8 4 4 0 000-8zM4 10a6 6 0 1112 0 6 6 0 01-12 0zm6 8a8 8 0 110-16 8 8 0 010 16z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="menu-item w-full p-2 flex items-center rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m0 0a3 3 0 010-6h6m0 6V4" />
                        </svg>
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Toggle submenu and store state in localStorage
        function toggleSubmenu() {
            const submenu = document.getElementById('manageSubmenu');
            submenu.classList.toggle('hidden');
            localStorage.setItem('submenuOpen', !submenu.classList.contains('hidden'));
        }

        // Restore submenu state on page load
        window.addEventListener('DOMContentLoaded', () => {
            const submenu = document.getElementById('manageSubmenu');
            const arrowIcon = document.getElementById('arrow-icon');
            const isOpen = localStorage.getItem('submenuOpen') === 'true';

            if (isOpen) {
                submenu.classList.remove('hidden');
                arrowIcon.classList.add('rotate-90');
            }
        });

        // Toggle sidebar visibility
        document.getElementById('toggle-button').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar-container');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('sidebar-backdrop-visible');
            backdrop.classList.toggle('sidebar-backdrop-hidden');
        });

        // Close sidebar when clicking on the backdrop (mobile only)
        document.getElementById('sidebar-backdrop').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar-container');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('sidebar-backdrop-visible');
            backdrop.classList.add('sidebar-backdrop-hidden');
        });

        // Close sidebar on link click (mobile view)
        document.querySelectorAll('#sidebar-container a').forEach(link => {
            link.addEventListener('click', () => {
                const sidebar = document.getElementById('sidebar-container');
                const backdrop = document.getElementById('sidebar-backdrop');
                if (window.innerWidth < 768) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.remove('sidebar-backdrop-visible');
                    backdrop.classList.add('sidebar-backdrop-hidden');
                }
            });
        });
    </script>

</body>
</html>
