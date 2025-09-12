<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**************************
     * Determine if the user is authorized to make this request.
     *************************/
    public function authorize(): bool
    {
        return true;
    }

    /**************************
     * Get the validation rules that apply to the request.
     *************************/
    public function rules(): array
    {
        return [
            'gender' => 'required|in:male,female',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'password' => 'nullable|string|min:6',
            'status' => 'boolean',
            'created_by' => 'nullable|integer',
        ];
    }
}
