<style>
/* Container styling */
.form-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
    background-color: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Group spacing */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Labels */
.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4a5568;
}

/* Input fields */
.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #f9fafb;
    color: #2d3748;
    outline: none;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input::placeholder {
    color: #a0aec0;
}

.form-input:focus {
    border-color: #63b3ed;
    box-shadow: 0 0 0 3px rgba(99, 179, 237, 0.5);
}

/* Submit Button */
.form-submit {
    text-align: center;
}

.form-button {
    width: 100%;
    padding: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #ffffff;
    background-color: #4299e1;
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}

.form-button:hover {
    background-color: #3182ce;
}

.form-button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}
</style>

<form method="POST" action="{{ route('profile.update') }}" class="form-container">
    @csrf
    @method('PATCH')

    <!-- Name -->
    <div class="form-group">
        <label for="name" class="form-label">{{ __('Name') }}</label>
        <input 
            id="name" 
            type="text" 
            name="name" 
            value="{{ old('name', Auth::user()->name) }}" 
            required 
            autofocus
            class="form-input"
            placeholder="Your Name"
        />
    </div>

    <!-- Email -->
    <div class="form-group">
        <label for="email" class="form-label">{{ __('Email') }}</label>
        <input 
            id="email" 
            type="email" 
            name="email" 
            value="{{ old('email', Auth::user()->email) }}" 
            required
            class="form-input"
            placeholder="you@example.com"
        />
    </div>

    <!-- Submit Button -->
    <div class="form-submit">
        <button type="submit" class="form-button">
            {{ __('Save Changes') }}
        </button>
    </div>
</form>
