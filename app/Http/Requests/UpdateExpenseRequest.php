<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Change this to true if you are handling authorization at the Request level
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Rules are generally similar to Store, but often require the 'sometimes' rule
        // for optional fields that shouldn't be required on update if not provided.
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'category' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            // The receipt is optional on update, and we may not want to delete the old one if a new one isn't uploaded.
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'], 
        ];
    }
}