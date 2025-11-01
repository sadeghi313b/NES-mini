<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
{
    /* -------------------------------------------------------------------------- */
    /*                                  authorize                                 */
    /* -------------------------------------------------------------------------- */
    public function authorize(): bool
    {
        // Only authenticated users can send requests
        return auth()->check();
    }

    /* -------------------------------------------------------------------------- */
    /*                                    rules                                   */
    /* -------------------------------------------------------------------------- */
    public function rules(): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'boolean'],
            'tags' => ['nullable', 'array'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];

        // --------------------------------------------
        // Make some fields optional during update
        // --------------------------------------------
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['date'][0] = 'sometimes';
            $rules['product_id'][0] = 'sometimes';
            $rules['quantity'][0] = 'sometimes';
            $rules['status'][0] = 'sometimes';
        }

        return $rules;
    }

    /* -------------------------------------------------------------------------- */
    /*                                  messages                                  */
    /* -------------------------------------------------------------------------- */
    public function messages(): array
    {
        return [
            'date.required' => 'Production date is required.',
            'product_id.required' => 'Product selection is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'status.required' => 'Status is required.',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                            prepareForValidation                            */
    /* -------------------------------------------------------------------------- */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
