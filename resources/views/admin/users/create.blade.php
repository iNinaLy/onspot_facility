@extends('layouts.admin')

@section('content')
<div class="container my-5" style="max-width: 1200px;">
    <h1 class="mb-4 text-center">Add New User</h1>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
            <div class="mt-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add Another User</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Go to Dashboard</a>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="shadow p-4 rounded bg-white" enctype="multipart/form-data">
        @csrf

        <!-- Username -->
        <div class="form-floating mb-3">
            <input type="text" name="username" class="form-control" id="username" required value="{{ old('username') }}" placeholder="Username">
            <label for="username">Username</label>
        </div>

        <!-- Name -->
        <div class="form-floating mb-3">
            <input type="text" name="name" class="form-control" id="name" required value="{{ old('name') }}" placeholder="Name">
            <label for="name">Name</label>
        </div>

        <!-- Email -->
        <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="email" required value="{{ old('email') }}" placeholder="Email">
            <label for="email">Email</label>
        </div>

        <!-- Phone Number -->
        <div class="form-floating mb-3">
            <input type="text" name="phone_no" class="form-control" id="phone_no" required value="{{ old('phone_no') }}" placeholder="Phone Number">
            <label for="phone_no">Phone Number</label>
        </div>

        <!-- Profile Picture -->
        <div class="mb-3">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic">
        </div>

        <!-- Password Field -->
        <div class="form-floating mb-3 position-relative">
            <input type="password" name="password" class="form-control" id="password" required placeholder="Password">
            <label for="password">Password</label>
            <span class="toggle-password" data-target="password" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                <i class="bi bi-eye-slash"></i>
            </span>
            <small id="passwordRequirements" class="form-text text-muted">
                Password must be at least 8 characters long and contain one uppercase letter, one lowercase letter, one digit, and one special character.
            </small>
            <small id="passwordFeedback" class="form-text text-danger d-none">Your password does not meet the requirements.</small>
        </div>

        <!-- Confirm Password Field -->
        <div class="form-floating mb-3 position-relative">
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required placeholder="Confirm Password">
            <label for="password_confirmation">Confirm Password</label>
            <span class="toggle-password" data-target="password_confirmation" style="cursor: pointer; position: absolute; top: 50%; right: 15px; transform: translateY(-50%);">
                <i class="bi bi-eye-slash"></i>
            </span>
            <small id="passwordError" class="text-danger d-none">Passwords do not match</small>
        </div>

        <!-- Role -->
        <div class="form-floating mb-4">
            <select name="role" id="role" class="form-control" required>
                <option value="" disabled selected>Select Role</option>
                <option value="officer" {{ old('role') == 'officer' ? 'selected' : '' }}>Officer</option>
                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                <option value="cleaner" {{ old('role') == 'cleaner' ? 'selected' : '' }}>Cleaner</option>
            </select>
            <label for="role">Role</label>
        </div>

        <!-- Building -->
        <div class="form-floating mb-4">
            <select name="building" id="building" class="form-control" required>
                <option value="" disabled selected>Select Building</option>
                <option value="Building A" {{ old('building') == 'Building A' ? 'selected' : '' }}>Building A</option>
                <option value="Building B" {{ old('building') == 'Building B' ? 'selected' : '' }}>Building B</option>
                <option value="Building C" {{ old('building') == 'Building C' ? 'selected' : '' }}>Building C</option>
            </select>
            <label for="building">Building</label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary" style="width: 15%;">Add User</button>
    </form>
</div>

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Add click event listener to all elements with class .toggle-password
        document.querySelectorAll('.toggle-password').forEach(item => {
            item.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (targetInput.getAttribute('type') === 'password') {
                    targetInput.setAttribute('type', 'text');
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    targetInput.setAttribute('type', 'password');
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });

        // Password mismatch validation
        document.getElementById('password_confirmation').addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const error = document.getElementById('passwordError');

            if (confirmPassword !== password) {
                error.classList.remove('d-none');
            } else {
                error.classList.add('d-none');
            }
        });

        // Password requirement validation
        const passwordInput = document.getElementById('password');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const passwordRequirements = document.getElementById('passwordRequirements');

        passwordInput.addEventListener('input', function () {
            const password = passwordInput.value;

            // Regular expression for password validation
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

            if (!regex.test(password)) {
                passwordFeedback.classList.remove('d-none');
                passwordRequirements.classList.add('text-danger');
            } else {
                passwordFeedback.classList.add('d-none');
                passwordRequirements.classList.remove('text-danger');
                passwordRequirements.classList.add('text-muted');
            }
        });
    });
</script>
@endsection

<!-- Include Bootstrap Icons in the head section -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
@endsection
