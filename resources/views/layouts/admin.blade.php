<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'OnSpot Facility'))</title>
    
    <!-- CSS Links -->
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.2.4/dist/cdn.min.js"></script>
    
    @stack('styles')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            transition: all 0.5s ease-in-out;
        }

        .main-content {
            margin-left: 250px; /* Sidebar width */
            padding: 20px;
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Main Navigation or Header -->
        @include('layouts.partials.admin-sidebar') <!-- Ensure you have a navbar partial -->

        <!-- Page Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- JS Links -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
