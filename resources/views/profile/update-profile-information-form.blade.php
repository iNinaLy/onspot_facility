<!-- resources/views/profile/partials/update-profile-information-form.blade.php -->

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <!-- Name -->
    <div class="mb-6">
        <label for="name" class="block text-gray-600 font-medium mb-2">{{ __('Name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required autofocus
            class="w-full px-4 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400
            bg-yellow-200 text-gray-800 placeholder-yellow-700"
            placeholder="Your Name">
    </div>

    <!-- Email -->
    <div class="mb-6">
        <label for="email" class="block text-gray-600 font-medium mb-2">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
            class="w-full px-4 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400
            bg-yellow-200 text-gray-800 placeholder-yellow-700"
            placeholder="you@example.com">
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit"
            class="px-6 py-2 bg-yellow-400 text-gray-800 font-bold rounded-md hover:bg-yellow-500 transition duration-300">
            {{ __('Save Changes') }}
        </button>
    </div>
</form>