@extends('layouts.admin')

@section('content')
<div class="container my-5">
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

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary" style="width: 15%;">Add User</button>
</form>

</div>

@section('scripts')
<script>
    // Ensure script runs after DOM content is fully loaded
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
    });
</script>
@endsection

<style>
    .form-floating label {
        padding: 0.5rem 1rem;
    }

    .alert {
        border-radius: 0.5rem;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
    }

    .btn-primary {
        background-color: #2E5675;
        border-color: #007bff;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #2e5679;
        border-color: #0056b3;
    }

    .shadow {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .rounded {
        border-radius: 0.5rem;
    }

    #passwordError {
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }
</style>
@endsection

<!-- Include Bootstrap Icons in the head section -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
