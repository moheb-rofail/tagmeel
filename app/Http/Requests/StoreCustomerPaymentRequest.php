<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }
    
    public function messages()
    {
        return [
            'amount.required' => 'مبلغ السداد مطلوب.',
            'amount.min' => 'يجب أن يكون مبلغ السداد أكبر من صفر.',
            'payment_date.required' => 'تاريخ السداد مطلوب.',
        ];
    }
}