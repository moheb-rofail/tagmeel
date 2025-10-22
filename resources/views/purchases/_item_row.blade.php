{{-- هذا الملف يمثل صفا واحدا لتفاصيل الشراء (PurchaseItem) --}}

@php
    $itemDetail = $itemDetail ?? null;
    $index = $index ?? time(); // استخدم time() أو عداد JS كـ index فريد
    $selectedItem = old("items.$index.item_id", $itemDetail->item_id ?? null);
@endphp

<div class="row purchase-item-row mb-2" data-index="{{ $index }}" dir="rtl">
    {{-- Item ID (Dropdown) --}}
    <div class="col-md-5">
        <label for="item_id_{{ $index }}" class="form-label visually-hidden">الصنف</label>
        <select class="form-select item-select @error("items.$index.item_id") is-invalid @enderror" 
                id="item_id_{{ $index }}" name="items[{{ $index }}][item_id]" required>
            <option value="" disabled {{ is_null($selectedItem) ? 'selected' : '' }}>اختر الصنف...</option>
            @foreach($itemsList as $item)
                <option value="{{ $item->id }}" data-cost="{{ $item->unit_price }}" {{ $selectedItem == $item->id ? 'selected' : '' }}>
                    {{ $item->name }} (سعر الوحدة الحالي: {{ number_format($item->unit_price, 2) }} ج.م)
                </option>
            @endforeach
        </select>
        @error("items.$index.item_id")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Quantity --}}
    <div class="col-md-2">
        <label for="quantity_{{ $index }}" class="form-label visually-hidden">الكمية</label>
        <input type="number" min="1" class="form-control item-quantity text-left @error("items.$index.quantity") is-invalid @enderror" 
               id="quantity_{{ $index }}" name="items[{{ $index }}][quantity]" 
               value="{{ old("items.$index.quantity", $itemDetail->quantity ?? 1) }}" placeholder="الكمية" required>
        @error("items.$index.quantity")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Unit Cost --}}
    <div class="col-md-2">
        <label for="unit_cost_{{ $index }}" class="form-label visually-hidden">تكلفة الوحدة</label>
        <input type="number" step="0.01" min="0.01" class="form-control item-cost text-left @error("items.$index.unit_cost") is-invalid @enderror" 
               id="unit_cost_{{ $index }}" name="items[{{ $index }}][unit_cost]" 
               value="{{ old("items.$index.unit_cost", $itemDetail->unit_cost ?? '') }}" placeholder="تكلفة الوحدة" required>
        @error("items.$index.unit_cost")
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sub Total (Readonly) --}}
    <div class="col-md-2">
        <label for="sub_total_{{ $index }}" class="form-label visually-hidden">الإجمالي الفرعي</label>
        <input type="number" step="0.01" class="form-control item-subtotal text-left" 
               id="sub_total_{{ $index }}" name="items[{{ $index }}][sub_total]" 
               value="{{ old("items.$index.sub_total", $itemDetail->sub_total ?? '') }}" readonly placeholder="الإجمالي الفرعي">
        {{-- حقل التحقق من الصحة مخفي --}}
        @error("items.$index.sub_total")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Remove Button --}}
    <div class="col-md-1 d-flex align-items-center">
        <button type="button" class="btn btn-danger btn-sm remove-item-row" title="حذف الصنف">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    {{-- Hidden ID field for Update --}}
    @if($itemDetail)
        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $itemDetail->id }}">
    @endif
</div>