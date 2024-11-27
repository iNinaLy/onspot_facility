<!-- resources/views/profile/partials/delete-user-form.blade.php -->


<form method="POST" action="{{ route('profile.destroy') }}">
    @csrf
    @method('DELETE')

    <p class="mb-6 text-gray-600">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
    </p>

    <!-- Password -->
    <div class="mb-6">
        <label for="password" class="block text-gray-600 font-medium mb-2">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" required
            class="w-full px-4 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400
            bg-red-200 text-gray-800 placeholder-red-700"
            placeholder="Enter Your Password">
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit"
            class="px-6 py-2 bg-red-400 text-gray-800 font-bold rounded-md hover:bg-red-500 transition duration-300">
            {{ __('Delete Account') }}
        </button>
    </div>
</form>