@extends('layouts.admin')

@section('title', 'Add New User')

@push('style')
    <link href="{{ asset('resources/admin/app.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="container mx-auto my-10 px-4 md:px-6 max-w-screen-xl">
    <!-- Page Title -->
    <div class="heading text-center mb-4">
        <h1 class="header-title">Add New User</h1>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" 
             style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" 
             style="background-color: #F8D7DA; border-color: #F5C6CB; padding: 1rem; font-weight: 400;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Multi-Step Form Container -->
    <div class="bg-white shadow-lg rounded-lg p-8">

        <form id="newUserForm" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf <!-- CSRF Token -->

            <!-- STEP 1: Role & Building -->
            <div id="step1" class="step active">
                <h2 class="text-xl font-bold mb-4">Basic Details</h2>

                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="form-label">Role <span class="text-red-500">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="cleaner" {{ old('role') == 'cleaner' ? 'selected' : '' }}>Cleaner</option>
                        <option value="officer" {{ old('role') == 'officer' ? 'selected' : '' }}>Officer</option>
                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    </select>
                </div>

                <!-- Building -->
                <div class="mb-4">
                    <label for="building" class="form-label">Building <span class="text-red-500">*</span></label>
                    <select class="form-select" id="building" name="building" required>
                        <option value="">Select Building</option>
                        <option value="Building A" {{ old('building') == 'Building A' ? 'selected' : '' }}>Building A</option>
                        <option value="Building B" {{ old('building') == 'Building B' ? 'selected' : '' }}>Building B</option>
                        <option value="Building C" {{ old('building') == 'Building C' ? 'selected' : '' }}>Building C</option>
                    </select>
                </div>

                <!-- Step 1 Buttons -->
                <div class="flex justify-end">
                    <button type="button" class="btn btn-primary" id="nextStep1">
                        Next
                    </button>
                </div>
            </div>

            <!-- STEP 2: Username, Name, Email, Phone -->
            <div id="step2" class="step hidden">
                <h2 class="text-xl font-bold mb-4">User Information</h2>

                <!-- Username -->
                <div class="form-floating mb-4">
                    <input type="text" name="username" class="form-control" id="username" placeholder="Username" 
                           value="{{ old('username') }}">
                    <label for="username">Username <span class="text-red-500">*</span></label>
                </div>

                <!-- Name -->
                <div class="form-floating mb-4">
                    <input type="text" name="name" class="form-control" id="name" placeholder="Name" 
                           value="{{ old('name') }}">
                    <label for="name">Name <span class="text-red-500">*</span></label>
                </div>

                <!-- Email -->
                <div class="form-floating mb-4">
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" 
                           value="{{ old('email') }}">
                    <label for="email">Email <span class="text-red-500">*</span></label>
                </div>

                <!-- Phone Number -->
                <div class="form-floating mb-4">
                    <input type="text" name="phone_no" class="form-control" id="phone_no" placeholder="Phone Number" 
                           value="{{ old('phone_no') }}">
                    <label for="phone_no">Phone Number <span class="text-red-500">*</span></label>
                </div>

                <!-- Step 2 Buttons -->
                <div class="flex justify-between">
                    <button type="button" class="btn btn-secondary" id="prevStep2">
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStep2">
                        Next
                    </button>
                </div>
            </div>

            <!-- STEP 3: Password & Profile Picture -->
            <div id="step3" class="step hidden">
                <h2 class="text-xl font-bold mb-4">Step 3: Security & Profile</h2>

                <!-- Password Field -->
                <div class="form-floating mb-4 position-relative">
                    <input type="password" name="password" class="form-control" id="password" 
                           placeholder="Password">
                    <label for="password">Password <span class="text-red-500">*</span></label>
                    <span class="toggle-password" data-target="password" 
                          style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                    <small id="passwordFeedback" class="form-text text-danger d-none">
                        Password must be at least 8 characters long, contain an uppercase letter, lowercase letter, digit, and special character.
                    </small>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-floating mb-4 position-relative">
                    <input type="password" name="password_confirmation" class="form-control" 
                           id="password_confirmation" placeholder="Confirm Password">
                    <label for="password_confirmation">Confirm Password <span class="text-red-500">*</span></label>
                    <span class="toggle-password" data-target="password_confirmation" 
                          style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                    <small id="passwordMatchError" class="text-danger d-none">
                        Passwords do not match.
                    </small>
                </div>

                <!-- Profile Picture -->
                <div class="mb-4">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                </div>

                <!-- Final Step Buttons -->
                <div class="flex justify-between">
                    <button type="button" class="btn btn-secondary" id="prevStep3">
                        Previous
                    </button>
                    <button type="submit" id="addUserButton" class="btn btn-primary" disabled>
                        Add User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')

@vite([
        'resources/admin/app.js',

    ])
    <!-- Bootstrap Bundle (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        // ------- Multi-step Wizard Logic -------
        document.addEventListener("DOMContentLoaded", function() {
            let currentStep = 1;
            const totalSteps = 3;

            const step1El = document.getElementById('step1');
            const step2El = document.getElementById('step2');
            const step3El = document.getElementById('step3');

            // Buttons
            const nextStep1 = document.getElementById('nextStep1');
            const nextStep2 = document.getElementById('nextStep2');
            const prevStep2 = document.getElementById('prevStep2');
            const prevStep3 = document.getElementById('prevStep3');

            // Show/hide steps
            function showStep(step) {
                step1El.classList.add('hidden');
                step2El.classList.add('hidden');
                step3El.classList.add('hidden');

                if (step === 1) step1El.classList.remove('hidden');
                if (step === 2) step2El.classList.remove('hidden');
                if (step === 3) step3El.classList.remove('hidden');
            }

            // Initial display
            showStep(currentStep);

            // Step button event listeners
            nextStep1.addEventListener('click', function() {
                currentStep = 2;
                showStep(currentStep);
            });

            nextStep2.addEventListener('click', function() {
                currentStep = 3;
                showStep(currentStep);
            });

            prevStep2.addEventListener('click', function() {
                currentStep = 1;
                showStep(currentStep);
            });

            prevStep3.addEventListener('click', function() {
                currentStep = 2;
                showStep(currentStep);
            });

            // ------- Password Validation Logic -------
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const passwordMatchError = document.getElementById('passwordMatchError');
            const addUserButton = document.getElementById('addUserButton');

            // Regex: 8 chars, upper/lowercase, digit, special char
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/;

            function validatePassword() {
                const isPasswordValid = passwordPattern.test(passwordInput.value);
                const doPasswordsMatch = (passwordInput.value === confirmPasswordInput.value);

                passwordFeedback.classList.toggle('d-none', isPasswordValid);
                passwordMatchError.classList.toggle('d-none', doPasswordsMatch || confirmPasswordInput.value === "");

                // Enable "Add User" only if password is valid & passwords match
                addUserButton.disabled = !(isPasswordValid && doPasswordsMatch);
            }

            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePassword);

            // ------- Toggle Password Visibility -------
            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (targetInput.getAttribute('type') === 'password') {
                        targetInput.setAttribute('type', 'text');
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    } else {
                        targetInput.setAttribute('type', 'password');
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    }
                });
            });
        });
    </script>
@endpush
@endsection
