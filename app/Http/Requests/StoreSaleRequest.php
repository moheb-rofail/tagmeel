<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // حقول Sale الرئيسية (Header)
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'invoice_number' => ['nullable', 'string', 'max:50', 'unique:sales,invoice_number'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_amount'],
            'final_amount' => ['required', 'numeric', 'min:0.01', 'lte:total_amount'],

            // NEW: payment status / paid amount
            'payment_status' => ['required', 'in:Not Paid,Partial,Paid'],
            'paid_amount' => ['required', 'numeric', 'min:0', 'lte:final_amount'],
            'notes' => ['nullable', 'string'],

            // حقول SaleItem (لبيانات الأصناف)
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'], // يجب إضافة قاعدة max للتحقق من المخزون هنا
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
            'payment_status.required' => 'يجب اختيار حالة الدفع.',
            'paid_amount.required' => 'يجب إدخال مبلغ المدفوع (اكتب 0 إذا غير مدفوع).',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $paymentStatus = $this->input('payment_status');
            $paidAmount = (float) $this->input('paid_amount', 0);
            $final = (float) $this->input('final_amount', 0);

            if ($paymentStatus === 'Paid' && abs($paidAmount - $final) > 0.01) {
                $v->errors()->add('paid_amount', 'المبلغ المدفوع يجب أن يساوي المبلغ النهائي إذا تم اختيار "مدفوع بالكامل".');
            }

            if ($paymentStatus === 'Not Paid' && $paidAmount > 0.009) {
                $v->errors()->add('paid_amount', 'المبلغ المدفوع يجب أن يكون صفر عند اختيار "غير مدفوع".');
            }

            if ($paymentStatus === 'Partial') {
                if (!($paidAmount > 0.009 && $paidAmount < $final - 0.009)) {
                    $v->errors()->add('paid_amount', 'المبلغ المدفوع يجب أن يكون أقل من المبلغ النهائي وأكبر من صفر عند اختيار "مدفوع جزئياً".');
                }
            }
        });
    }



}