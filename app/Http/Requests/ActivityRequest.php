<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add policy-based control later if needed
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert JSON string to array if necessary
        if (is_string($this->input('interchangeable-bundle'))) {
            $decoded = json_decode($this->input('interchangeable-bundle'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['interchangeable-bundle' => $decoded]);
            }
        }

        // Cast status to boolean
        if ($this->has('status')) {
            $this->merge(['status' => (bool) $this->input('status')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'                       => ['required', 'string', 'max:32'],
            'alias'                      => ['nullable', 'string', 'max:32'],
            'zone'                       => ['nullable', 'string', 'max:32'],
            'common_standard_tv_time'    => ['nullable', 'integer', 'min:0', 'max:65535'],
            'common_standard_ref_time'   => ['nullable', 'integer', 'min:0', 'max:65535'],
            'interchangeable-bundle'     => ['nullable', 'array'],
            'description'                => ['nullable', 'string'],
            'status'                     => ['boolean'],
            'tags'                       => ['nullable', 'array'],
        ];
    }

    /**
     * Custom validation messages (optional)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The activity name is required.',
            'name.max'      => 'The name may not be greater than 32 characters.',
            'common_standard_tv_time.integer' => 'TV time must be an integer.',
            'common_standard_ref_time.integer' => 'Ref time must be an integer.',
        ];
    }
}
