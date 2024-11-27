<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Sidebar Styling */
        #sidebar-container {
            background: linear-gradient(180deg, #ffffff 0%, #f4f7fa 100%);
            scrollbar-width: thin;
            scrollbar-color: rgba(150, 150, 150, 0.3) transparent;
        }

        /* Custom Scrollbar */
        #sidebar-container::-webkit-scrollbar {
            width: 8px;
        }

        #sidebar-container::-webkit-scrollbar-thumb {
            background-color: rgba(150, 150, 150, 0.3);
            border-radius: 10px;
        }

        /* Sleek Sidebar Transition */
        .sidebar-enter {
            transform: translateX(-100%);
            opacity: 0;
        }

        .sidebar-enter-active {
            transform: translateX(0);
            opacity: 1;
            transition: transform 0.4s ease, opacity 0.4s ease;
        }

        /* Backdrop Blur */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 40;
            transition: opacity 0.3s ease;
        }

        /* Link Styling */
        .menu-item {
            transition: all 0.3s ease;
            color: #4a5568;
        }

        .menu-item:hover {
            background-color: rgba(46, 86, 117, 0.1);
        }

        .active-link {
            background-color: #2e5675;
            color: white;
            font-weight: 600;
        }

        /* Profile Styling */
        .profile-container img {
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Arrow Rotation Animation */
        .rotate-90 {
            transform: rotate(90deg);
            transition: transform 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="sidebar-backdrop hidden"></div>

    <!-- Sidebar Container -->
    <div id="sidebar-container" class="fixed inset-y-0 left-0 w-64 md:w-72 shadow-lg transform -translate-x-full md:translate-x-0 z-50 rounded-r-3xl">
        <div class="h-full flex flex-col overflow-y-auto">

            <!-- Profile Section -->
            @php
                $adminUser = \App\Models\User::where('role', 'admin')->first();
            @endphp
            <div class="flex items-center py-4 px-5 border-b">
                @if($adminUser)
                    <div class="flex items-center profile-container">
                        <img src="{{ $adminUser->profile_pic ? 'data:image/jpeg;base64,' . base64_encode($adminUser->profile_pic) : 'https://via.placeholder.com/40' }}"
                             alt="Profile Picture"
                             class="w-12 h-12 mr-3">
                        <div>
                            <div class="text-sm font-bold">{{ $adminUser->name }}</div>
                            <div class="text-xs text-gray-400">{{ $adminUser->email }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400">Admin user not found.</p>
                @endif
                <button id="toggle-button" class="ml-auto p-2 focus:outline-none md:hidden">
                    <img src="{{ asset('img/svg/menu.svg') }}" alt="Menu Icon" class="h-6 w-6">
                </button>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 px-4 py-6 space-y-3">

                <!-- Home Link -->
                <a href="{{ route('admin.dashboard') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                    <img src="{{ asset('img/svg/home.svg') }}" alt="Home Icon" class="h-5 w-5 mr-3">
                    Dashboard
                </a>

                <!-- Manage Dropdown -->
                <div>
                    <button class="menu-item w-full p-2 flex items-center justify-between rounded-lg" onclick="toggleSubmenu()">
                        <span class="flex items-center">
                            <img src="{{ asset('img/svg/manage.svg') }}" alt="Manage Icon" class="h-5 w-5 mr-3">
                            Manage
                        </span>
                        <img id="arrow-icon" src="{{ asset('img/svg/chevron.svg') }}" alt="Arrow Icon" class="h-5 w-5 transition-transform">
                    </button>

                    <!-- Manage Submenu -->
                    <div id="manageSubmenu" class="hidden pl-6 space-y-2">
                        <a href="{{ route('admin.officers') }}" class="menu-item p-2 block rounded-lg">
                            Officer
                        </a>
                        <a href="{{ route('admin.supervisors.index') }}" class="menu-item p-2 block rounded-lg">
                            Supervisor
                        </a>
                        <a href="{{ route('admin.cleaners') }}" class="menu-item p-2 block rounded-lg">
                            Cleaner
                        </a>
                    </div>
                </div>

                <!-- Complaints Link -->
                <a href="{{ route('admin.complaints') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.complaints') ? 'active-link' : '' }}">
                    <img src="{{ asset('img/svg/complaint.svg') }}" alt="Complaints Icon" class="h-5 w-5 mr-3">
                    Complaints
                </a>

                <!-- New User Link -->
                <a href="{{ route('admin.users.create') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.users.create') ? 'active-link' : '' }}">
                    <img src="{{ asset('img/svg/user.svg') }}" alt="New User Icon" class="h-5 w-5 mr-3">
                    New User
                </a>

            </div>

            <!-- Account Section -->
            <div class="px-4 py-4 border-t border-gray-200 space-y-2">
                <a href="{{ route('admin.profile.edit') }}" class="menu-item p-2 flex items-center rounded-lg {{ request()->routeIs('admin.profile.edit') ? 'active-link' : '' }}">
                    <img src="{{ asset('img/svg/profile.svg') }}" alt="Profile Icon" class="h-5 w-5 mr-3">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="menu-item w-full p-2 flex items-center rounded-lg">
                        <img src="{{ asset('img/svg/logout.svg') }}" alt="Logout Icon" class="h-5 w-5 mr-3 text-red-500">
                        Log Out
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Toggle submenu and store state in localStorage
        function toggleSubmenu() {
            const submenu = document.getElementById('manageSubmenu');
            const arrowIcon = document.getElementById('arrow-icon');
            
            submenu.classList.toggle('hidden');
            arrowIcon.classList.toggle('rotate-90'); // Toggle rotation class for animation
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
    </script>

</body>

</html>