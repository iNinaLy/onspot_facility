@extends('layouts.admin')

@section('content')
<div class="container mx-auto my-10 px-4 md:px-6 max-w-screen-xl">
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-8 flex-col md:flex-row">
        <h1 class="text-3xl font-semibold text-gray-900 text-center md:text-left">Admin Profile</h1>
    </div>

    <!-- Success or Error Message -->
    @if(session('success'))
        <div class="alert alert-success bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md mb-8" role="alert">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill mr-2 text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 px-4 py-3 shadow-md mb-8" role="alert">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill mr-2 text-red-500"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Profile Information Section -->
    <div class="p-8 bg-white shadow-lg rounded-lg mb-8">
        <h3 class="text-2xl font-bold text-gray-700 mb-6">
            {{ __('Update Profile Information') }}
        </h3>
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password Section -->
    <div class="p-8 bg-white shadow-lg rounded-lg mb-8">
        <h3 class="text-2xl font-bold text-gray-700 mb-6">
            {{ __('Change Password') }}
        </h3>
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account Section -->
    <div class="p-8 bg-white shadow-lg rounded-lg">
        <h3 class="text-2xl font-bold text-red-600 mb-6">
            {{ __('Delete Account') }}
        </h3>
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

<!-- Additional Styles -->
<style>
    .container {
        font-family: 'Poppins', sans-serif;
    }

    .alert-success {
        color: #3A533B;
        background-color: #EBF5E1;
        border-color: #A6D785;
    }

    .alert-danger {
        color: #B52A2A;
        background-color: #FDE2E1;
        border-color: #E53E3E;
    }

    .alert {
        display: flex;
        align-items: center;
        border-radius: 5px;
        padding: 1rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }

    .bi {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
</style>