<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeadlineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'part_quantity' => 'required|integer|min:1000|max:10000',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ];

        // For update requests, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                return 'sometimes|' . $rule;
            }, $rules);
        }

        return $rules;
    }
}