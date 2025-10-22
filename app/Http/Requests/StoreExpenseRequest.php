<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Change this to true if you are handling authorization at the Request level
        // or keep it false and handle it in the Controller/Middleware.
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'category' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            // Add validation for the receipt file upload
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'], 
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This is useful for handling the file path, or preparing data before validation.
     */
    protected function prepareForValidation()
    {
        // Example: If you are accepting a file named 'receipt', you might prepare its path here
        // However, file handling (move, path storage) is often better done in the Controller
    }
}