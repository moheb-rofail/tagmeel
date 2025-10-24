<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Return Header Fields
            'return_type' => ['required', 'string', 'in:sale,purchase'],
            'reference_id' => ['nullable', 'integer'], // Validation should ensure it exists in Sale/Purchase table if provided
            'return_date' => ['required', 'date', 'before_or_equal:today'],
            'customer_supplier_name' => ['nullable', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:Processed,Pending Refund,Pending Credit'],

            // Return Item Fields
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_value' => ['required', 'numeric', 'min:0'],
            'items.*.sub_total' => ['required', 'numeric', 'min:0'],
        ];
    }
    
    public function messages()
    {
        return [
            'return_type.in' => 'يجب أن يكون نوع الإرجاع إما بيع أو شراء.',
            'items.required' => 'يجب إضافة صنف واحد على الأقل لعملية الإرجاع.',
            'total_amount.min' => 'المبلغ الإجمالي لا يمكن أن يكون سالباً.',
        ];
    }
}