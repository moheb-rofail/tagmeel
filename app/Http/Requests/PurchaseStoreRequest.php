<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseStoreRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مصرحًا له بتقديم هذا الطلب.
     */
    public function authorize(): bool
    {
        // لغرض التصحيح (Debugging)، اجعلها تعيد TRUE مؤقتًا لتجاوز التحقق من الصلاحية.
        // يجب أن يتم تعديلها لاحقاً لضمان أن المستخدم لديه صلاحية الشراء (مثل role = admin/staff).
        return true; 
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            // التحقق من بيانات المواد المشتراة
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'], // **هذا هو المشتبه به الرئيسي**
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost_at_purchase' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    // ... (بقية الملف كما هو)
}
