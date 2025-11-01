<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStandardTimeRequest extends FormRequest
{
    //. -------------------------------------------------------------------------- */
    //.                                authorize                                   */
    //. -------------------------------------------------------------------------- */
    public function authorize(): bool
    {
        return true;
    }


    //. -------------------------------------------------------------------------- */
    //.                                prepareForValidation                        */
    //. -------------------------------------------------------------------------- */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN),
        ]);
    }


    //. -------------------------------------------------------------------------- */
    //.                                  rules                                     */
    //. -------------------------------------------------------------------------- */
    public function rules(): array
    {
        $rules = [
            'product_id'            => ['required', 'integer', 'exists:products,id'],
            'activity_id'           => ['required', 'integer', 'exists:activities,id'],
            'standard_time'        => ['required', 'numeric', 'between:0,99999.999'],
            'description'           => ['nullable', 'string'],
            'status'                => ['nullable', 'boolean'],
            'tags'                  => ['nullable', 'array'],
            'tags.*'                => ['string'],
            'created_by'            => ['nullable', 'integer', 'exists:users,id'],
        ];

        // If the request method is PUT or PATCH → we are in update mode
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Make the fields optional during update
            $rules['product_id'][0] = 'sometimes';
            $rules['activity_id'][0] = 'sometimes';
            $rules['stanndard_time'][0] = 'sometimes';
        }

        return $rules;
    }


    //. -------------------------------------------------------------------------- */
    //.                                messages                                   */
    //. -------------------------------------------------------------------------- */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists'   => 'Selected product does not exist.',
            'activity_id.required' => 'Activity ID is required.',
            'activity_id.exists'   => 'Selected activity does not exist.',
            'stanndard_time.required' => 'Standard time is required.',
            'stanndard_time.numeric'  => 'Standard time must be a numeric value.',
            'status.required' => 'Status field is required.',
        ];
    }

    
}
