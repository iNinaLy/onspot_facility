@extends('layouts.admin')

@section('title', 'Manage Officers')

@push('styles')
<style>
    .form-control {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.75rem;
        padding-right: 2.5rem; /* Extra padding for the eye icon */
        width: 100%;
    }

    .form-control:focus {
        border-color: #4C7F9D;
        box-shadow: 0 0 0 3px rgba(76, 127, 157, 0.3);
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto my-10 px-6 max-w-screen-md">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Edit Officer</h1>
        <p class="text-gray-600">Update the officer's details below.</p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="text-red-500">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Officer Form -->
    <form action="{{ route('admin.officers.update', $officer->id) }}" method="POST" enctype="multipart/form-data" class="p-6 bg-white rounded-lg shadow">
        @csrf
        @method('PUT')

        <!-- Username Field -->
        <div class="mb-6 relative">
            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $officer->username) }}" placeholder="Enter Username" required>
        </div>

        <!-- Name Field -->
        <div class="mb-6 relative">
            <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $officer->name) }}" placeholder="Enter Name" required>
        </div>

        <!-- Email Field -->
        <div class="mb-6 relative">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $officer->email) }}" placeholder="Enter Email" required>
        </div>

        <!-- Phone Number Field -->
        <div class="mb-6 relative">
            <label for="phone_no" class="block text-gray-700 font-medium mb-2">Phone Number</label>
            <input type="text" name="phone_no" id="phone_no" class="form-control" value="{{ old('phone_no', $officer->phone_no) }}" placeholder="Enter Phone Number" required>
        </div>

        <!-- Profile Picture Field -->
        <div class="mb-6 relative">
            <label for="profile_pic" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" class="form-control" onchange="previewImage(event)">

            <!-- Preview selected image -->
            <div class="mt-4" id="profile-pic-container" style="display: none;">
                <img id="profile-pic-preview" src="" alt="Profile Picture" class="w-24 h-24 rounded-full border-2 border-gray-300 object-cover">
            </div>
        </div>

        <!-- Reset Password Button -->
        <div class="mb-6">
            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                Reset Password
            </button>
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.officers') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-all">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all">Save</button>
        </div>
    </form>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.officers.resetPassword', $officer->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <!-- New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password" class="form-control" id="new_password" placeholder="New Password" required>
                        <label for="new_password">New Password</label>
                        <span class="position-absolute toggle-password" data-target="new_password">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        <small id="passwordFeedback" class="form-text text-danger d-none">
                            Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a digit, and a special character.
                        </small>
                    </div>

                    <!-- Confirm New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" placeholder="Confirm Password" required>
                        <label for="new_password_confirmation">Confirm Password</label>
                        <span class="position-absolute toggle-password" data-target="new_password_confirmation">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        <small id="passwordMatchError" class="text-danger d-none">Passwords do not match.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const passwordMatchError = document.getElementById('passwordMatchError');

        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

        function validatePassword() {
            const isPasswordValid = passwordPattern.test(passwordInput.value);
            const doPasswordsMatch = passwordInput.value === confirmPasswordInput.value;

            passwordFeedback.classList.toggle('d-none', isPasswordValid);
            passwordMatchError.classList.toggle('d-none', doPasswordsMatch || confirmPasswordInput.value === "");
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

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profile-pic-preview');
            const container = document.getElementById('profile-pic-container');
            output.src = reader.result;
            container.style.display = 'block'; // Show the image container when an image is chosen
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
@endsection
