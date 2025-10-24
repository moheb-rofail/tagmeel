<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Rules are generally the same as Store, but often require checking the item IDs if deleting/updating details.
        return [
            'return_type' => ['required', 'string', 'in:sale,purchase'],
            'reference_id' => ['nullable', 'integer'],
            'return_date' => ['required', 'date', 'before_or_equal:today'],
            'customer_supplier_name' => ['nullable', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:Processed,Pending Refund,Pending Credit'],

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
        ];
    }
}