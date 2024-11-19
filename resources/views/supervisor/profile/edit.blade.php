<x-app-layout>
    <div class="py-12 bg-gradient-to-r from-yellow-300 via-orange-200 to-pink-200 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
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
</x-app-layout>