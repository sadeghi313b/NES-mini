<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return true;
        // return Gate::allows('canAccessOrder', $this->route('order') ?? Order::class);

        $user = $this->user();

        if ($this->isMethod('POST')) {
            // برای ایجاد
            return Gate::allows('create', Order::class);
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // برای ویرایش
            $order = $this->route('order'); // گرفتن نمونه از URL
            return Gate::allows('edit', $order);
        }

        return false;
    }

    // این متد قبل از اجرای قوانین صدا زده میشود
    protected function prepareForValidation(): void
    {
        if ($this->has('seen')) {
            $this->merge([
                'seen' => strtolower((string) $this->input('seen')) === 'seen',
            ]);
        }
        // dd($this->status);
    }

    public function rules(): array
    {

        $rules = [
            'product_id' => 'required|exists:products,id',
            'month_id' => 'required|exists:months,id',
            'quantity' => 'required|integer|min:1|max:500000',
            'notification_date' => 'required|date',
            'seen' => 'boolean',
            'status' => ['required', new Enum(OrderStatus::class)],
            'description' => 'nullable|string',
        ];

        // For update requests, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $newRules = [];
            foreach ($rules as $key => $rule) {
                if (is_array($rule)) {
                    $newRules[$key] = $rule; // اگر آرایه بود، تغییر نده
                } else {
                    $newRules[$key] = 'sometimes|' . $rule;
                }
            }
            $rules = $newRules;
        }

        return $rules;
    }
}
