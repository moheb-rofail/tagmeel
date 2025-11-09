{{-- Pass the $return, $itemsList, $salesList, and $purchasesList variables safely --}}
@php
    $returnDoc = $return ?? null;
    $itemsList = $itemsList ?? [];
    $salesList = $salesList ?? [];
    $purchasesList = $purchasesList ?? [];
    $selectedType = old('return_type', $returnDoc->return_type ?? 'sale');
    $selectedReference = old('reference_id', $returnDoc->reference_id ?? null);
@endphp

<div class="row" dir="rtl">
    {{-- Return Type --}}
    <div class="col-md-4 mb-3">
        <label for="return_type" class="form-label">نوع الإرجاع <span class="text-danger">*</span></label>
        <select class="form-select @error('return_type') is-invalid @enderror" id="return_type" name="return_type" required>
            <option value="sale" {{ $selectedType == 'sale' ? 'selected' : '' }}>إرجاع مبيعات (مخزون داخل)</option>
            <option value="purchase" {{ $selectedType == 'purchase' ? 'selected' : '' }}>إرجاع مشتريات (مخزون خارج)</option>
        </select>
        @error('return_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Reference ID --}}
    <div class="col-md-4 mb-3">
        <label for="reference_id" class="form-label">مرجع الفاتورة الأصلية (اختياري)</label>
        <select class="form-select @error('reference_id') is-invalid @enderror" id="reference_id" name="reference_id">
            <option value="" selected>لا يوجد مرجع محدد</option>
            <optgroup label="مبيعات (لإرجاع المبيعات)">
                @foreach($salesList as $sale)
                    <option value="{{ $sale->id }}" data-type="sale" {{ $selectedReference == $sale->id && $selectedType == 'sale' ? 'selected' : '' }}>
                        بيع #{{ $sale->id }} - ({{ $sale->sale_date->format('Y-m-d') }}) - {{ number_format($sale->final_amount, 2) }} ج
                    </option>
                @endforeach
            </optgroup>
            <optgroup label="مشتريات (لإرجاع المشتريات)">
                @foreach($purchasesList as $purchase)
                    <option value="{{ $purchase->id }}" data-type="purchase" {{ $selectedReference == $purchase->id && $selectedType == 'purchase' ? 'selected' : '' }}>
                        شراء #{{ $purchase->id }} - ({{ $purchase->purchase_date->format('Y-m-d') }}) - {{ number_format($purchase->total_amount, 2) }} ج
                    </option>
                @endforeach
            </optgroup>
        </select>
        @error('reference_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Return Date --}}
    <div class="col-md-4 mb-3">
        <label for="return_date" class="form-label">تاريخ الإرجاع <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('return_date') is-invalid @enderror" id="return_date" name="return_date"
               value="{{ old('return_date', $returnDoc ? $returnDoc->return_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('return_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row" dir="rtl">
    {{-- Customer/Supplier Name --}}
    <div class="col-md-6 mb-3">
        <label for="customer_supplier_name" class="form-label">اسم العميل/المورد</label>
        <input type="text" class="form-control @error('customer_supplier_name') is-invalid @enderror" id="customer_supplier_name" name="customer_supplier_name"
               value="{{ old('customer_supplier_name', $returnDoc->customer_supplier_name ?? '') }}" placeholder="اسم العميل أو المورد">
        @error('customer_supplier_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">حالة الإرجاع <span class="text-danger">*</span></label>
        @php
            $selectedStatus = old('status', $returnDoc->status ?? 'Processed');
        @endphp
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="Processed" {{ $selectedStatus == 'Processed' ? 'selected' : '' }}>تمت المعالجة</option>
            <option value="Pending Refund" {{ $selectedStatus == 'Pending Refund' ? 'selected' : '' }}>بانتظار الاسترداد المالي</option>
            <option value="Pending Credit" {{ $selectedStatus == 'Pending Credit' ? 'selected' : '' }}>بانتظار إشعار دائن</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>
<h5 class="mb-3">تفاصيل الأصناف المرجعة (Return Items)</h5>

<div id="return-items-container">
    {{-- Include item rows here --}}
    @if ($returnDoc && $returnDoc->items->isNotEmpty())
        @foreach($returnDoc->items as $index => $itemDetail)
            @include('returns._item_row', ['index' => $index, 'itemDetail' => $itemDetail, 'itemsList' => $itemsList])
        @endforeach
    @else
        @include('returns._item_row', ['index' => 0, 'itemDetail' => null, 'itemsList' => $itemsList])
    @endif
</div>

<div class="mb-3 text-left">
    <button type="button" class="btn btn-sm btn-info" id="add-return-item-row">
        + إضافة صنف جديد
    </button>
</div>

<hr>
<h5 class="mb-3">ملخص الإرجاع</h5>

<div class="row" dir="rtl">
    {{-- Total Amount --}}
    <div class="col-md-6 mb-3">
        <label for="total_amount" class="form-label">المبلغ الإجمالي المرتجع/المعكوس <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount"
               value="{{ old('total_amount', $returnDoc->total_amount ?? 0) }}" required readonly>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Reason --}}
<div class="mb-3" dir="rtl">
    <label for="reason" class="form-label">سبب الإرجاع <span class="text-danger">*</span></label>
    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason', $returnDoc->reason ?? '') }}</textarea>
    @error('reason')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<script type="text/javascript" src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
{{-- ****************************************************** --}}
{{-- *************** JAVASCRIPT FOR CALCULATIONS ************** --}}
{{-- ****************************************************** --}}
<script>
    $(document).ready(function() {
        let nextIndex = {{ $returnDoc ? $returnDoc->items->count() : 1 }};

        function calculateSubTotal(row) {
            let quantity = parseFloat(row.find('.item-quantity').val()) || 0;
            let value = parseFloat(row.find('.item-value').val()) || 0;
            let subTotal = quantity * value;

            row.find('.item-subtotal').val(subTotal.toFixed(2));
            calculateTotalAmount();
        }

        function calculateTotalAmount() {
            let total = 0;
            $('.return-item-row').each(function() {
                let subTotal = parseFloat($(this).find('.item-subtotal').val()) || 0;
                total += subTotal;
            });
            $('#total_amount').val(total.toFixed(2));
        }

        // Add New Item Row
        $('#add-return-item-row').on('click', function() {
            const container-xl = $('#return-items-container');
            
            let newRow = $(`
                <div class="row return-item-row mb-2" data-index="${nextIndex}" dir="rtl">
                    <div class="col-md-5">
                        <select class="form-select item-select" name="items[${nextIndex}][item_id]" required>
                            <option value="" disabled selected>اختر الصنف...</option>
                            @foreach($itemsList as $item)
                                <option value="{{ $item->id }}" data-selling="{{ $item->selling_price }}" data-cost="{{ $item->unit_price }}">
                                    {{ $item->name }} (بيع: {{ number_format($item->selling_price, 2) }} / تكلفة: {{ number_format($item->unit_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" class="form-control item-quantity text-left" name="items[${nextIndex}][quantity]" value="1" placeholder="الكمية" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0" class="form-control item-value text-left" name="items[${nextIndex}][unit_value]" placeholder="قيمة الوحدة" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control item-subtotal text-left" name="items[${nextIndex}][sub_total]" readonly placeholder="الإجمالي الفرعي">
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item-row" title="حذف الصنف">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);

            container.append(newRow);
            nextIndex++;
        });

        // Event Handlers for calculations
        $('#return-items-container').on('input', '.item-quantity, .item-value', function() {
            let row = $(this).closest('.return-item-row');
            calculateSubTotal(row);
        });
        
        // Item Select: Set default value based on return type
        $('#return-items-container').on('change', '.item-select', function() {
            let row = $(this).closest('.return-item-row');
            let selectedOption = $(this).find(':selected');
            let returnType = $('#return_type').val();
            let defaultValue = 0;

            if (returnType === 'sale') {
                // Sale Return uses selling price as default refund value
                defaultValue = selectedOption.data('selling') || 0;
            } else if (returnType === 'purchase') {
                // Purchase Return uses unit cost as default credit value
                defaultValue = selectedOption.data('cost') || 0;
            }
            
            row.find('.item-value').val(defaultValue.toFixed(2));
            calculateSubTotal(row);
        });

        // Change Handler for Return Type (to trigger item value updates)
        $('#return_type').on('change', function() {
            $('.return-item-row').each(function() {
                let row = $(this);
                let selectedOption = row.find('.item-select').find(':selected');
                let returnType = $('#return_type').val();
                let defaultValue = 0;

                if (returnType === 'sale') {
                    defaultValue = selectedOption.data('selling') || 0;
                } else if (returnType === 'purchase') {
                    defaultValue = selectedOption.data('cost') || 0;
                }
                
                // Only update the value field if it's currently empty (to avoid overwriting user changes)
                // You might need a more robust check here based on business rules.
                if (parseFloat(row.find('.item-value').val()) === 0 || row.find('.item-value').val() === "") {
                     row.find('.item-value').val(defaultValue.toFixed(2));
                }
                
                calculateSubTotal(row);
            });
        });

        // Remove Row
        $('#return-items-container').on('click', '.remove-item-row', function() {
            $(this).closest('.return-item-row').remove();
            calculateTotalAmount();
        });

        // Initial Calculations
        $('.return-item-row').each(function() {
             calculateSubTotal($(this));
        });
        calculateTotalAmount();
    });
</script>