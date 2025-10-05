<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DeadlineRequest extends FormRequest
{
    public static function deadlinesRules(): array
    {
        return [
            'deadlines' => 'array',
            'deadlines.*.part_quantity' => 'nullable|integer|min:1|max:10000',
            'deadlines.*.due_date'      => 'nullable|date',
            'deadlines.*.description'   => 'nullable|string',
            'deadlines.*.status'        => 'nullable|boolean',
        ];
        //*usage: public function rules(): array { return self::deadlinesRules(); }
    }


    /* -------------------------------------------------------------------------- */
    /*          Determine if the user is authorized to make this request          */
    /* -------------------------------------------------------------------------- */
    public function authorize(): bool
    {
        // return Gate::allows('create',Order::class);
        return true;
    }

    /* -------------------------------------------------------------------------- */
    /*                                    rules :me                               */
    /* -------------------------------------------------------------------------- */
    protected function baseRules(): array
    {
        return [
            'part_quantity' => 'nullable|integer|min:1|max:10000',
            'due_date'      => 'nullable|date',
            'description'   => 'nullable|string',
            'status'        => 'nullable|boolean',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*             Get the validation rules that apply to the request             */
    /* -------------------------------------------------------------------------- */
    public function rules(): array
    {
        $rules = [
            'order_id'  => 'required|exists:orders,id',
        ] + $this->baseRules();

        // For update requests, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                return 'sometimes|' . $rule;
            }, $rules);
        }

        return $rules;
    }

    /* -------------------------------------------------------------------------- */
    /*                                     me                                     */
    /* -------------------------------------------------------------------------- */
    public function validateDeadlines(): array
    {
        $deadlines = $this->input('deadlines', []);
        dd($deadlines);

        foreach ($deadlines as $index => $deadline) {
            $validator = Validator::make($deadline, $this->baseRules());

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    "deadlines.$index" => $validator->errors()->all(),
                ]);
            }
        }

        return $deadlines;
    }
}