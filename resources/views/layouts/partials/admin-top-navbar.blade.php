<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Admin Dashboard') }}</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.2.4/dist/cdn.min.js"></script>
    <style>
        .main-content {
            transition: margin-left 0.35s ease-in-out, padding 0.35s ease-in-out; /* Smooth transition for margin and padding */
            padding: 20px; /* Default padding for main content */
            margin-top: 80px; /* Adjust for top navbar height */
        }

        #sidebar.expand ~ .main-content {
            margin-left: 260px; /* Adjust margin for expanded sidebar */
            padding-left: 30px; /* Additional padding for expanded sidebar */
        }

        #sidebar:not(.expand) ~ .main-content {
            margin-left: 70px; /* Adjust margin for collapsed sidebar */
            padding-left: 10px; /* Additional padding for collapsed sidebar */
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Include Sidebar -->
        @include('layouts.partials.admin-sidebar')

        <!-- Include Top Navbar -->
        @include('layouts.partials.admin-top-navbar')

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        // Script to toggle sidebar expansion
        const hamBurger = document.querySelector(".toggle-btn");
        const sidebar = document.querySelector("#sidebar");
        const topNav = document.querySelector(".top-nav");
        const mainContent = document.querySelector(".main-content");

        hamBurger.addEventListener("click", function () {
            sidebar.classList.toggle("expand");

            // Rotate the toggle button when expanding/collapsing
            hamBurger.classList.toggle("rotate");

            // Adjust top navigation and main content
            if (sidebar.classList.contains("expand")) {
                topNav.style.marginLeft = '260px';
                topNav.style.width = 'calc(100% - 260px)';
                mainContent.style.marginLeft = '260px';
                mainContent.style.width = 'calc(100% - 260px)';
            } else {
                topNav.style.marginLeft = '70px';
                topNav.style.width = 'calc(100% - 70px)';
                mainContent.style.marginLeft = '70px';
                mainContent.style.width = 'calc(100% - 70px)';
            }

            // Automatically close all submenus when sidebar is collapsed
            if (!sidebar.classList.contains("expand")) {
                document.querySelectorAll(".sidebar-item.has-submenu").forEach(item => {
                    item.classList.remove("open");
                });
            }
        });

        // Toggle submenu on click
        function toggleSubmenu(event, element) {
            event.preventDefault();
            element.parentElement.classList.toggle("open");
        }

        // Close sidebar and submenu when clicking outside of the sidebar
        document.addEventListener("click", function (event) {
            if (sidebar.classList.contains("expand") &&
                !sidebar.contains(event.target) &&
                !event.target.closest(".toggle-btn") &&
                !event.target.closest(".top-nav")
            ) {
                sidebar.classList.remove("expand");
                hamBurger.classList.remove("rotate");

                // Adjust top navigation and main content
                topNav.style.marginLeft = '70px';
                topNav.style.width = 'calc(100% - 70px)';
                mainContent.style.marginLeft = '70px';
                mainContent.style.width = 'calc(100% - 70px)';

                // Close all open submenus
                document.querySelectorAll(".sidebar-item.has-submenu").forEach(item => {
                    item.classList.remove("open");
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
