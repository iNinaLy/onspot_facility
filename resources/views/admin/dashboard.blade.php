<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <!-- Left side: Logo and Navigation Links -->
            <div class="flex items-center space-x-4">
                <img src="images/logo.png" alt="Logo" class="h-10 w-10 rounded-full"> <!-- Adjust logo path and size -->
                <a href="#" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">Home</a>
                <a href="#" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">Cleaners</a>
                <a href="#" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">Complaints</a>
                <a href="#" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">History</a>
            </div>
            
            <!-- Right side: Supervisor Dropdown and Log out Button -->
            <div class="relative inline-block text-left">
                <button type="button" class="bg-teal-600 text-white px-4 py-2 rounded-lg flex items-center" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">
                    Supervisor
                    <i class="bi bi-chevron-down ml-2"></i> <!-- Bootstrap Icon for dropdown -->
                </button>

                <!-- Dropdown menu -->
                <div class="absolute right-0 z-10 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg origin-top-right ring-1 ring-black ring-opacity-5 hidden" id="dropdownMenu">
                    <div class="py-1" role="none">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Settings</a>
                    </div>
                </div>
            </div>
            <!-- Reduced margin to bring buttons closer -->
            <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition ml-1">Log out</button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Welcome to Your Dashboard</h3>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Action Button
                        </button>
                    </div>
                    <div class="mt-4">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Dropdown visibility control */
        #dropdownMenuButton:focus + #dropdownMenu,
        #dropdownMenuButton:active + #dropdownMenu {
            display: block; /* Show dropdown on button focus or click */
        }

        /* Hide dropdown by default */
        #dropdownMenu {
            display: none; 
        }
    </style>

    <script>
        // Toggle dropdown visibility on button click
        document.getElementById('dropdownMenuButton').addEventListener('click', function() {
            var dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('#dropdownMenuButton')) {
                var dropdowns = document.getElementsByClassName("hidden");
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.add('hidden');
                }
            }
        };
    </script>
</x-app-layout>
