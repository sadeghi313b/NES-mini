<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'gender' => 'nullable|in:male,female',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'status' => 'boolean',
            'created_by' => 'nullable|integer',
        ];
    }
}
