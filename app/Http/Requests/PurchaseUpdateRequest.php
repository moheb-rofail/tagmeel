<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PurchaseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only 'admin' or 'staff' should be able to update purchases
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $purchaseId = $this->route('purchase')->id;

        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255', Rule::unique('purchases')->ignore($purchaseId)],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', Rule::in(['pending', 'paid', 'partial'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
