@extends('layouts.admin')

@section('title', 'Edit Cleaner')

@push('styles')
<style>
    /* General Form Control Styling */
    .form-control {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.75rem;
        width: 100%;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        outline: none;
    }

    /* Toggle Password Visibility Icon */
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6b7280;
    }

    /* Button Styling */
    .btn {
        transition: all 0.3s ease;
        color: #2e5675;
    }

    .btn:hover {
        opacity: 0.9;
    }

    /* Building Select Field Styling */
    .building-select {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.75rem;
        width: 100%;
        appearance: none; /* Remove default arrow */
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23999" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 011.708-.708L8 10.293l4.646-4.647a.5.5 0 11.708.708l-5 5a.5.5 0 01-.708 0l-5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .building-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    /* Profile Picture Styling */
    .profile-pic-preview {
        width: 96px; /* 24 * 4 */
        height: 96px;
        border-radius: 9999px; /* Fully rounded */
        border: 2px solid #d1d5db;
        object-fit: cover;
    }

    /* File Input Styling */
    .file-input {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.75rem;
        width: 100%;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .file-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    /* Modal Styling Enhancements */
    .modal-content {
        border-radius: 0.5rem;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    /* Responsive Adjustments */
    @media (max-width: 640px) {
        .profile-pic-preview {
            width: 72px;
            height: 72px;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto my-10 px-6 max-w-screen-md">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Edit Cleaner</h1>
        <p class="text-gray-600">Update the cleaner's details below.</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            {{ session('success') }}
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="text-green-500">&times;</span>
            </button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-bs-dismiss="alert" aria-label="Close">
                <span class="text-red-500">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Cleaner Form -->
    <form action="{{ route('admin.cleaners.update', $cleaner->id) }}" method="POST" enctype="multipart/form-data" class="p-6 bg-white rounded-lg shadow">
        @csrf
        @method('PUT')

        
        
        <!-- Username Field -->
        <div class="mb-6 relative">
            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $cleaner->cleaner_username) }}" placeholder="Enter Username" required>
            @error('username')
                <small class="text-red-500 mt-1">{{ $message }}</small>
            @enderror
        </div>

        <!-- Name Field -->
        <div class="mb-6 relative">
            <label for="cleaner_name" class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" name="cleaner_name" id="cleaner_name" class="form-control" value="{{ old('cleaner_name', $cleaner->cleaner_name) }}" placeholder="Enter Name" required>
            @error('cleaner_name')
                <small class="text-red-500 mt-1">{{ $message }}</small>
            @enderror
        </div>

        <!-- Phone Number Field -->
        <div class="mb-6 relative">
            <label for="phone_no" class="block text-gray-700 font-medium mb-2">Phone Number</label>
            <input type="text" name="phone_no" id="phone_no" class="form-control" value="{{ old('phone_no', $cleaner->cleaner_phoneNo) }}" placeholder="Enter Phone Number" required>
            @error('phone_no')
                <small class="text-red-500 mt-1">{{ $message }}</small>
            @enderror
        </div>

        <!-- Building Field with Static Options -->
        <div class="mb-6 relative">
            <label for="building" class="block text-gray-700 font-medium mb-2">Building</label>
            <select name="building" id="building" class="building-select" required>
                <option value="">Select Building</option>
                <option value="Building A" {{ old('building', $cleaner->building) == 'Building A' ? 'selected' : '' }}>Building A</option>
                <option value="Building B" {{ old('building', $cleaner->building) == 'Building B' ? 'selected' : '' }}>Building B</option>
                <option value="Building C" {{ old('building', $cleaner->building) == 'Building C' ? 'selected' : '' }}>Building C</option>
            </select>
            @error('building')
                <small class="text-red-500 mt-1">{{ $message }}</small>
            @enderror
        </div>

        <!-- Profile Picture Field -->
        <div class="mb-6 relative">
            <label for="profile_pic" class="block text-gray-700 font-medium mb-2">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" class="file-input" accept="image/*" onchange="previewImage(event)">
            @error('profile_pic')
                <small class="text-red-500 mt-1">{{ $message }}</small>
            @enderror

            <!-- Profile Picture Preview -->
            <div class="mt-4">
            <label for="profile_pic" class="block text-gray-700 font-medium mb-2">Current Profile Picture</label>
                @if(method_exists($cleaner, 'getFirstMediaUrl') && $cleaner->getFirstMediaUrl('profile_pictures'))
                    <!-- Using Spatie's Media Library -->
                    <img id="profile-pic-preview" 
                         src="{{ asset($cleaner->getFirstMediaUrl('profile_pictures')) }}" 
                         alt="Profile Picture" 
                         class="profile-pic-preview">
                @elseif(isset($cleaner->profile_pic) && $cleaner->profile_pic)
                    <!-- Storing Image Directly in Database -->
                    <img id="profile-pic-preview" 
                         src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" 
                         alt="Profile Picture" 
                         class="profile-pic-preview">
                @else
                    <!-- Placeholder Image -->
                    <img id="profile-pic-preview" 
                         src="{{ asset('images/placeholder.png') }}" 
                         alt="Profile Picture" 
                         class="profile-pic-preview">
                @endif
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
            <a href="{{ route('admin.cleaners') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-all">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all">Save</button>
        </div>
    </form>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password for {{ $cleaner->cleaner_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.cleaners.resetPassword', $cleaner->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password" class="form-control" id="new_password" placeholder="New Password" required>
                        <label for="new_password">New Password</label>
                        <span class="toggle-password" data-target="new_password">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        @error('new_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small id="passwordFeedback" class="form-text text-danger d-none">
                            Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a digit, and a special character.
                        </small>
                    </div>

                    <!-- Confirm New Password Field -->
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" placeholder="Confirm Password" required>
                        <label for="new_password_confirmation">Confirm Password</label>
                        <span class="toggle-password" data-target="new_password_confirmation">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                        @error('new_password_confirmation')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
    // Function to preview the selected image
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profile-pic-preview');
            output.src = reader.result;
            // If using database storage and no image exists, ensure the container is displayed
            const container = document.getElementById('profile-pic-container');
            if(container){
                container.style.display = 'block';
            }
        };
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Toggle password visibility
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

        // Password validation logic
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const passwordMatchError = document.getElementById('passwordMatchError');

        if(passwordInput && confirmPasswordInput){
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

            function validatePassword() {
                const isPasswordValid = passwordPattern.test(passwordInput.value);
                const doPasswordsMatch = passwordInput.value === confirmPasswordInput.value;

                // Show or hide password strength feedback
                if (!isPasswordValid) {
                    passwordFeedback.classList.remove('d-none');
                } else {
                    passwordFeedback.classList.add('d-none');
                }

                // Show or hide password match error
                if (!doPasswordsMatch && confirmPasswordInput.value !== "") {
                    passwordMatchError.classList.remove('d-none');
                } else {
                    passwordMatchError.classList.add('d-none');
                }
            }

            // Validate passwords on input
            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePassword);
        }
    });
</script>
@endpush
@endsection
