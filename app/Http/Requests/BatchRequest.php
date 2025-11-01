<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /* -------------------------------------------------------------------------- */
    /*                                batchesRules                                */
    /* -------------------------------------------------------------------------- */
    public static function batchesRules(): array
    {
        return [
            'batches' => 'array',
            'batches.*.cut_id' => 'nullable|integer|exists:cuts,id',
            'batches.*.size' => 'nullable|integer',
            'batches.*.created_by' => 'nullable|integer',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'cut_id' => 'required|exists:cuts,id',
            'quantity' => 'required|integer|in:300,500',
            'printing_date' => 'nullable|date',
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
