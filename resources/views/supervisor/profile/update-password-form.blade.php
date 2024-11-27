<!-- resources/views/profile/partials/update-password-form.blade.php -->

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('PUT')

    <!-- Current Password -->
    <div class="mb-6">
        <label for="current_password" class="block text-gray-600 font-medium mb-2">{{ __('Current Password') }}</label>
        <input id="current_password" type="password" name="current_password" required
            class="w-full px-4 py-2 border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400
            bg-pink-200 text-gray-800 placeholder-pink-700"
            placeholder="Current Password">
    </div>

    <!-- New Password -->
    <div class="mb-6">
        <label for="password" class="block text-gray-600 font-medium mb-2">{{ __('New Password') }}</label>
        <input id="password" type="password" name="password" required
            class="w-full px-4 py-2 border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400
            bg-pink-200 text-gray-800 placeholder-pink-700"
            placeholder="New Password">
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label for="password_confirmation" class="block text-gray-600 font-medium mb-2">{{ __('Confirm Password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
            class="w-full px-4 py-2 border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400
            bg-pink-200 text-gray-800 placeholder-pink-700"
            placeholder="Confirm Password">
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit"
            class="px-6 py-2 bg-pink-400 text-gray-800 font-bold rounded-md hover:bg-pink-500 transition duration-300">
            {{ __('Update Password') }}
        </button>
    </div>
</form>
