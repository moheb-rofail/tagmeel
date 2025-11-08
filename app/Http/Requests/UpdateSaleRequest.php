<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale')->id;

        return [
            // حقول Sale الرئيسية (Header)
            'customer_name' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'invoice_number' => ['nullable', 'string', 'max:50', Rule::unique('sales')->ignore($saleId)],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'final_amount' => ['required', 'numeric', 'min:0.01', 'lte:total_amount'],
            'notes' => ['nullable', 'string'],

            // حقول SaleItem (لبيانات الأصناف)
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'items.*.sub_total' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages()
    {
        return [
            'final_amount.lte' => 'المبلغ النهائي يجب أن لا يتجاوز المبلغ الإجمالي.',
            'discount_amount.lte' => 'قيمة الخصم يجب أن لا تتجاوز المبلغ الإجمالي.',
            'items.required' => 'يجب إضافة صنف واحد على الأقل لعملية البيع.',
        ];
    }
}