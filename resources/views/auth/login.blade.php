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
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- MDBootstrap CSS (for custom elements) -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css"
    />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /*
         * Overall Page Styles
         */
        body {
            background:
                linear-gradient(rgba(28,54,79,0.6), rgba(28,54,79,0.6)),
                url('images/login.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Figtree', sans-serif;
            color: #f8f9fa; /* Light text to contrast the overlay */
            margin: 0;
            padding: 0;
        }

        /* Title / Branding Section */
        .title-section {
            text-align: left;
        }
        .title-section h1 {
            font-size: 4rem;
            font-weight: 1000;
            margin-bottom: 1rem;
            text-align: center;
        }

        .title-section p {
            font-size: 1rem;
            max-width: 450px;
            margin: 0 auto;
            line-height: 1.5;
            
        }

        /* Card (Glassmorphism) */
        .glass-card {
            background: #dce9eb;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            color: #333;
        }

        /* Form Labels */
        .form-label {
            font-weight: 600;
            color: #333;
        }

        .btn-signin {
            background: linear-gradient(135deg, #1c2325, #507cb2);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 0.55rem 1rem;
            font-size: 1rem;
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-in-out;
            display: inline-block;
            width: 100%;
        }

        .btn-signin:hover {
           
            transform: translateY(-2px) scale(1);
        }

        .btn-signin:active {
            /* Pressed state */
            transform: translateY(0) scale(1.0);
        }

        /* Button Glow Animation on Hover */
        .btn-signin:hover {
            animation: glow 1.0s ease-in-out infinite alternate;
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 10px rgb(124, 179, 195);
            }
            100% {
                box-shadow: 0 0 10px rgb(159, 184, 214);
            }
        }

        /* Error Messages & Alerts */
        .error-message {
            color: #d9534f;
            font-size: 0.875rem;
        }
        .alert {
            border-radius: 0.5rem;
        }

        /* Optional subtle fade-in animation for the card */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <section class="vh-100 d-flex align-items-center">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">

                <!-- Left Section (Title / Branding) -->
                <div class="col-md-8 col-lg-7 col-xl-6 d-none d-md-block title-section">
                    <h1 class="text-white">OnSpot Facility</h1>
                    <p class="text-white">
                        Making your facility management seamless and efficient.
                    </p>
                </div>

                <!-- Right Section (Login Form) -->
                <div class="col-md-7 col-lg-5 col-xl-5">
                    <div class="glass-card fade-in">
                        <h3 class="text-center mb-4">Welcome Back</h3>
                        <p class="text-muted text-center mb-4">Log in to continue</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!-- Email input -->
                            <div class="form-outline mb-4">
                                <input 
                                    type="email"
                                    id="email"
                                    class="form-control form-control-lg"
                                    name="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    value="{{ old('email') }}"
                                />
                                <label class="form-label" for="email">Email address</label>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password input -->
                            <div class="form-outline mb-4">
                                <input
                                    type="password"
                                    id="password"
                                    class="form-control form-control-lg"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                />
                                <label class="form-label" for="password">Password</label>
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remember Me and Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="remember_me"
                                        name="remember"
                                    >
                                    <label
                                        class="form-check-label"
                                        for="remember_me"
                                    >
                                        Remember me
                                    </label>
                                </div>
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-decoration-none"
                                >
                                    Forgot password?
                                </a>
                            </div>

                            <!-- Sign in button -->
                            <button
                                type="submit"
                                class="btn-signin"
                            >
                                Sign in
                            </button>

                            <!-- Server-Side Error Handling -->
                            @if(session('error'))
                                <div class="alert alert-danger mt-3" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div><!-- End of Right Section -->
            </div>
        </div>
    </section>

    <!-- Bootstrap JS and MDBootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    ></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.js"
    ></script>
    
</body>


</html>
