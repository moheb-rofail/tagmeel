<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ItemUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only 'admin' or 'staff' can update item details
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $itemId = $this->route('item')->id; // Get the ID of the item being updated

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('items')->ignore($itemId)],
            'description' => ['nullable', 'string'],
            // Stock quantity and pricing can be updated
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'reorder_point' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'gte:unit_price'],
        ];
    }
}
