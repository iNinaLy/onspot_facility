<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Login') }}</title>
    <link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MDBootstrap CSS (for custom elements) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color:#fff;/* Matches primary color for consistency */
            font-family: 'Figtree', sans-serif;
            color: #f8f9fa; /* Light color for contrast */
        }

        .card {
            border-radius: 1.5rem;
            box-shadow: 0 12px 20px #597693;
            background: #f7f7f7;
            padding: 2rem;
            color: #333; /* Text color in cards */
        }

        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #bbb;
        }

        .form-outline {
            position: relative;
            color: #555;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .btn-primary {
            background-color: #1c364f;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #244b71;
        }
        /* Enhanced Error Message Styling */
        .error-message {
            color: #d9534f;
            font-size: 0.875rem;
        }

        /* Adjust alert style for session messages */
        .alert {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <section class="vh-100 d-flex align-items-center">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <!-- Side Image Section -->
                <div class="col-md-8 col-lg-7 col-xl-6 d-none d-md-block">
                    <img src="images/bgimage.png"  alt="Illustrative image">
                </div>

                <!-- Form Card Section -->
                <div class="col-md-7 col-lg-5 col-xl-5">
                    <div class="card p-5">
                        <h3 class="text-center mb-4">Welcome Back</h3>
                        <p class="text-muted text-center mb-4">Log in to continue</p>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="email" id="email" class="form-control form-control-lg" name="email" required autofocus autocomplete="username" value="{{ old('email') }}" />
                                <label class="form-label" for="email">Email address</label>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password input -->
                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="password" id="password" class="form-control form-control-lg" name="password" required autocomplete="current-password" />
                                <label class="form-label" for="password">Password</label>
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remember Me and Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                    <label class="form-check-label" for="remember_me"> Remember me </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                            </div>

                            <!-- Sign in button -->
                            <button type="submit" class="btn btn-primary btn-lg btn-block w-100">Sign in</button>

                            <!-- Server-Side Error Handling -->
                            @if(session('error'))
                                <div class="alert alert-danger mt-3" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS and MDBootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.js"></script>
</body>
</html>