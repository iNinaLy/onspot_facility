<title>{{ config('app.name','OnSpot Facility') }}</title>
<link rel="icon" href="{{ asset('images/favicon-32x32.png') }}" type="image/png">

@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="container mx-auto my-10 px-4 md:px-6 max-w-screen-xl">
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-8 flex-col md:flex-row">
        <h1 class="text-3xl font-semibold text-gray-900 text-center md:text-left">Add New User</h1>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="background-color: #F8D7DA; border-color: #F5C6CB; padding: 1rem; font-weight: 400;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- New User Form -->
    <div class="bg-white shadow-lg rounded-lg p-8">
        <form id="newUserForm" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf <!-- CSRF Token -->

            <!-- Username -->
            <div class="form-floating mb-4">
                <input type="text" name="username" class="form-control" id="username" placeholder="Username" required value="{{ old('username') }}">
                <label for="username">Username</label>
            </div>

            <!-- Name -->
            <div class="form-floating mb-4">
                <input type="text" name="name" class="form-control" id="name" placeholder="Name" required value="{{ old('name') }}">
                <label for="name">Name</label>
            </div>

            <!-- Email -->
            <div class="form-floating mb-4">
                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required value="{{ old('email') }}">
                <label for="email">Email</label>
            </div>

            <!-- Phone Number -->
            <div class="form-floating mb-4">
                <input type="text" name="phone_no" class="form-control" id="phone_no" placeholder="Phone Number" required value="{{ old('phone_no') }}">
                <label for="phone_no">Phone Number</label>
            </div>

            <!-- Profile Picture -->
            <div class="mb-4">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="cleaner" {{ old('role') == 'cleaner' ? 'selected' : '' }}>Cleaner</option>
                    <option value="officer" {{ old('role') == 'officer' ? 'selected' : '' }}>Officer</option>
                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                </select>
            </div>

            <!-- Building -->
            <div class="mb-4">
                <label for="building" class="form-label">Building</label>
                <select class="form-select" id="building" name="building" required>
                    <option value="">Select Building</option>
                    <option value="Building A" {{ old('building') == 'Building A' ? 'selected' : '' }}>Building A</option>
                    <option value="Building B" {{ old('building') == 'Building B' ? 'selected' : '' }}>Building B</option>
                    <option value="Building C" {{ old('building') == 'Building C' ? 'selected' : '' }}>Building C</option>
                </select>
            </div>

            <!-- Password Field -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                <label for="password">Password</label>
                <span class="toggle-password" data-target="password" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                    <i class="bi bi-eye-slash"></i>
                </span>
                <small id="passwordFeedback" class="form-text text-danger d-none">
                    Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one digit, and one special character.
                </small>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-floating mb-4 position-relative">
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" required>
                <label for="password_confirmation">Confirm Password</label>
                <span class="toggle-password" data-target="password_confirmation" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                    <i class="bi bi-eye-slash"></i>
                </span>
                <small id="passwordMatchError" class="text-danger d-none">Passwords do not match.</small>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="addUserButton" class="btn btn-primary w-100" disabled>Add User</button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const passwordMatchError = document.getElementById('passwordMatchError');
        const addUserButton = document.getElementById('addUserButton');

        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

        function validatePassword() {
            const isPasswordValid = passwordPattern.test(passwordInput.value);
            const doPasswordsMatch = passwordInput.value === confirmPasswordInput.value;

            passwordFeedback.classList.toggle('d-none', isPasswordValid);
            passwordMatchError.classList.toggle('d-none', doPasswordsMatch || confirmPasswordInput.value === "");

            addUserButton.disabled = !(isPasswordValid && doPasswordsMatch);
        }

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

        passwordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
    });
    

</script>

@endpush

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
@endsection
