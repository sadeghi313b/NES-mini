<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'gender' => ['required', new Enum(Gender::class)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ];

        // For update requests, modify rules
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Make fields optional for update
            $rules = array_map(function ($rule) {
                return 'sometimes|' . $rule;
            }, $rules);

            // For email uniqueness, ignore current user
            if ($this->has('email')) {
                $userId = $this->route('user')?->id ?? $this->user->id ?? null;
                if ($userId) {
                    $rules['email'] = 'sometimes|email|max:255|unique:users,email,' . $userId;
                }
            }

            // Password is not required for updates
            if (isset($rules['password'])) {
                $rules['password'] = str_replace('required', 'sometimes', $rules['password']);
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'gender.in' => 'The selected gender is invalid. Please select male or female.',
            'email.unique' => 'This email address is already taken.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}