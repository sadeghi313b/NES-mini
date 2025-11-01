<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkRequest extends FormRequest
{
    //. -------------------------------------------------------------------------- */
    //.                                authorize                                   */
    //. -------------------------------------------------------------------------- */
    public function authorize(): bool
    {
        return true;
    }

    //. -------------------------------------------------------------------------- */
    //.                           prepareForValidation                             */
    //. -------------------------------------------------------------------------- */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   rules                                    */
    //. -------------------------------------------------------------------------- */
    public function rules(): array
    {
        $rules = [
            'date'          => ['required', 'date'],
            'employee_id'   => ['required', 'integer', 'exists:employees,id'],
            'work_type'     => ['required', 'in:Batched,nonBatched,Extra,noWork,Vacation'],
            'description'   => ['nullable', 'string'],
            'status'        => ['nullable', 'boolean'],
            'tags'          => ['nullable', 'array'],
            'tags.*'        => ['string'],
            'created_by'    => ['nullable', 'integer', 'exists:users,id'],
        ];

        // If the request method is PUT or PATCH → we are in update mode
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Make the fields optional during update
            $rules['date'][0] = 'sometimes';
            $rules['employee_id'][0] = 'sometimes';
            $rules['work_type'][0] = 'sometimes';
        }

        return $rules;
    }

    //. -------------------------------------------------------------------------- */
    //.                                 messages                                   */
    //. -------------------------------------------------------------------------- */
    public function messages(): array
    {
        return [
            'date.required'         => 'Date field is required.',
            'date.date'             => 'Date must be a valid date format.',
            'employee_id.required'  => 'Employee ID is required.',
            'employee_id.exists'    => 'Selected employee does not exist.',
            'work_type.required'    => 'Work type is required.',
            'work_type.in'          => 'Invalid work type selected.',
            'status.boolean'        => 'Status must be true or false.',
        ];
    }
}
