<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'cable_name' => 'required|string|max:64',
            'cable_length' => 'required|integer|min:0|max:300',
            'cable_color' => 'required|string|in:gray,white,black',
            'cable_tall_strip_length' => 'required|integer|min:0|max:50',
            'cable_short_strip_length' => 'required|integer|min:0|max:10',
            'blue_wire_cut_length' => 'required|integer|min:0|max:10',
            'brown_wire_cut_length' => 'required|integer|min:0|max:10',
            'yellow_wire_cut_length' => 'required|integer|min:0|max:10',
            'blue_wire_strip_length' => 'required|integer|min:0|max:6',
            'brown_wire_strip_length' => 'required|integer|min:0|max:6',
            'yellow_wire_strip_length' => 'required|integer|min:0|max:6',
            'blue_wire_applicator_id' => 'required|exists:applicators,id',
            'brown_wire_applicator_id' => 'required|exists:applicators,id',
            'yellow_wire_applicator_id' => 'required|exists:applicators,id',
            'molds_id' => 'required|exists:molds,id',
            'cord_length' => 'required|integer|min:5|max:20',
            'double_wire_applicator_id' => 'required|exists:applicators,id',
            'double_wire_length' => 'required|integer|min:15|max:30',
            'plug_type' => 'required|in:Ref,Tv,Triple',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ];

        // For update requests, make all fields optional (sometimes)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                return 'sometimes|' . $rule;
            }, $rules);
        }

        return $rules;
    }
}
