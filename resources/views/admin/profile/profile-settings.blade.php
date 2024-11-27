@extends('layouts.admin')

@section('content')
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    /* Main Layout */
    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(252deg, #f5f7fa, #dfe6eb);
        color: #333;
    }

    /* Card Styling */
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        background-color: #fff;
        margin-bottom: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Dark Mode */
    .dark-mode {
        background-color: #1d1f21;
        color: #f5f5f5;
    }

    .dark-mode .card {
        background-color: #2e2e3e;
        color: #f5f5f5;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    /* Button Styling */
    #darkModeToggle {
        background-color: #2e5675;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }

    #darkModeToggle:hover {
        background-color: #1f3d5a;
    }

    /* Form Input Styling */
    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 0.95rem;
    }

    /* Button Styling for Save Changes */
    .btn-save {
        background-color: #2e5675;
        color: #fff;
        border-radius: 8px;
        padding: 0.5rem 1rem;
    }

    .btn-save:hover {
        background-color: #1f3d5a;
    }
</style>

<div class="main-content-wrapper fade-in">
    <!-- Header Section -->
    <div class="header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-dark">Profile Settings</h1>
        <div class="profile d-flex align-items-center">
            <button id="darkModeToggle" class="btn btn-sm">Dark Mode</button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Profile Information -->
        <div class="col-lg-6">
            <!-- Update Profile Information Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Update Profile Information</h5>
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group mb-3">
                            <label for="name" class="key-metric">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="key-metric">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" required>
                        </div>
                        <button type="submit" class="btn-save mt-2">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Password and Account Management -->
        <div class="col-lg-6">
            <!-- Update Password Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Change Password</h5>
                    <form method="POST" action="{{ route('admin.password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="current_password" class="key-metric">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_password" class="key-metric">New Password</label>
                            <input type="password" id="new_password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="key-metric">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn-save mt-2">Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold text-danger mb-3">Delete Account</h5>
                    <p class="text-muted">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    <form method="POST" action="{{ route('admin.profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="form-group mb-3">
                            <label for="password" class="key-metric">Confirm Password</label>
                            <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password to confirm">
                        </div>
                        <button type="submit" class="btn btn-danger mt-2">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Dark Mode Toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        document.querySelectorAll('.card').forEach(card => card.classList.toggle('dark-mode'));
    });
</script>
@endsection