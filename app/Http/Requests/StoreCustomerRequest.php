<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكن تعديلها لاحقاً لمنح الصلاحية للمستخدمين المعتمدين فقط
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:customers,name'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'balance_type' => ['required', 'in:Debit,Credit'],
            'notes' => ['nullable', 'string'],
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'اسم البائع/الزبون مطلوب.',
            'name.unique' => 'هذا الاسم مسجل بالفعل.',
            'balance_type.required' => 'يجب تحديد نوع الرصيد الافتتاحي (مدين/دائن).',
        ];
    }
}