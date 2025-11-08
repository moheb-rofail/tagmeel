<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // عند التحديث، نستخدم ignore() للسماح بنفس الاسم الحالي
            'name' => ['required', 'string', 'max:255', 'unique:customers,name,' . $this->customer->id], 
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            // يُفضل عدم تحديث initial_balance مباشرة، لكن نتركه للنموذج
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
        ];
    }
}