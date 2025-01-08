<!-- resources/views/profile/edit.blade.php -->

@extends('layouts.app')

@section('title', 'Profile Settings')

@push('styles')
    <style>
        .heading {
            font-size: 2rem;
            font-weight: 700rem;
            color: #2E5675;
        }
        
        @media (min-width: 640px) {
            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-top: 1rem;
                padding-right: 1.5rem;
            }
        }
         
        .toggle-content {
            display: none;
        }
        .toggle-content.active {
            display: block;
        }
    </style>
@endpush

@section('content')
<div class="py-12 bg-gradient-to-b from-gray-100 via-gray to-blue-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Profile Settings Heading -->
            <div class="heading">
                <h1 class="text-[2rem] text-[#2e5657] mb-8">{{ __('Profile Settings') }}</h1>
            </div>

            <!-- Profile Information -->
            <div class="p-8 bg-white rounded-lg shadow-md">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6">
                    {{ __('Update Profile Information') }}
                </h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-8 bg-white rounded-lg shadow-md">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6">
                    {{ __('Change Password') }}
                </h3>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-8 bg-white rounded-lg shadow-md">
                <h3 class="text-2xl font-semibold text-red-500 mb-6">
                    {{ __('Delete Account') }}
                </h3>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@vite([
    'resources/supervisor/app.js',
    'resources/supervisor/dashboard.js',
])
    <script>
        // Function to toggle the visibility of the details section
        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            if (details) {
                details.classList.toggle('active');
            }
        }

    </script>
@endpush
