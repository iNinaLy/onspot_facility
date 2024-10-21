<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
            return [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],  // Ensure username is required
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique(User::class)->ignore($this->user()->id),
                ],
                'phone_no' => ['nullable', 'string', 'max:20'],  // Optional field for phone number
                'profile_pic' => ['nullable', 'file', 'max:2048'],  // Optional: profile picture upload
            ];
        }
    
        /**
         * Log the validation rules for debugging purposes.
         */
        protected function passedValidation()
        {
            // Log the validated data to check what is being validated
            \Log::info('Validated data in ProfileUpdateRequest:', $this->validated());
        }
    
        /**
         * Force the request to expect JSON responses.
         */
        public function wantsJson()
        {
            return true; // Force JSON responses for validation errors
        }
    }
    

