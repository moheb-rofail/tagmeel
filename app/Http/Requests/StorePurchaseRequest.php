<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            // حقول Purchase الرئيسية (Header)
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'invoice_number' => ['nullable', 'string', 'max:50', 'unique:purchases,invoice_number'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', 'in:Pending,Paid,Partial'],
            'notes' => ['nullable', 'string'],
            
            // حقول PurchaseItem (لبيانات الأصناف)
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0.01'],
            'items.*.sub_total' => ['required', 'numeric', 'min:0.01'],
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'supplier_id.required' => 'يجب اختيار المورد.',
            'items.required' => 'يجب إضافة صنف واحد على الأقل لفاتورة الشراء.',
            'items.*.item_id.exists' => 'الصنف المختار غير موجود في قاعدة البيانات.',
        ];
    }
}