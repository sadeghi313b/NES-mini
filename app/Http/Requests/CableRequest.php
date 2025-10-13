<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CableRequest extends FormRequest
{
    /* -------------------------------------------------------------------------- */
    /*                                authorization                               */
    /* -------------------------------------------------------------------------- */
    public function authorize(): bool
    {
        return true; 
    }

    /* -------------------------------------------------------------------------- */
    /*                                    rules                                   */
    /* -------------------------------------------------------------------------- */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'status' => 'boolean',
            'color' => 'required|in:' . implode(',', \App\Models\Cable::COLORS),
        ];

        // For update requests, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $newRules = [];
            foreach ($rules as $key => $rule) {
                if (is_array($rule)) {
                    $newRules[$key] = $rule; // If the rule is an array, do not change it
                } else {
                    $newRules[$key] = 'sometimes|' . $rule;
                }
            }
            $rules = $newRules;
        }

        return $rules;
    }
}
