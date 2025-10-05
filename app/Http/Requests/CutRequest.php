<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CutRequest extends FormRequest
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
            'quantity' => 'required|integer|min:1000|max:3000',
            'maximum_batch_size' => 'required|integer|in:300,500',
            'printing_date' => 'nullable|date',
            'cutting_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
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