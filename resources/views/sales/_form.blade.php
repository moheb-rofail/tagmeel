{{-- Pass the $sale and $itemsList variables safely --}}
@php
    $sale = $sale ?? null;
    $itemsList = $itemsList ?? [];
@endphp

<div class="row" dir="rtl">
    {{-- Sale Date --}}
    <div class="col-md-4 mb-3">
        <label for="sale_date" class="form-label">تاريخ البيع <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('sale_date') is-invalid @enderror" id="sale_date" name="sale_date"
               value="{{ old('sale_date', $sale ? $sale->sale_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('sale_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Invoice Number --}}
    <div class="col-md-4 mb-3">
        <label for="invoice_number" class="form-label">رقم الفاتورة</label>
        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number"
               value="{{ old('invoice_number', $sale->invoice_number ?? '') }}" placeholder="رقم الفاتورة (اختياري)">
        @error('invoice_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Customer Name --}}
    <div class="col-md-4 mb-3">
        <label for="customer_name" class="form-label">اسم العميل</label>
        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name"
               value="{{ old('customer_name', $sale->customer_name ?? '') }}" placeholder="عميل نقدي أو اسم العميل">
        @error('customer_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>
<h5 class="mb-3">تفاصيل الأصناف المباعة (Sale Items)</h5>

<div id="sale-items-container">
    {{-- هنا سيتم توليد حقول الأصناف ديناميكياً بواسطة JavaScript --}}

    @if ($sale && $sale->items->isNotEmpty())
        @foreach($sale->items as $index => $itemDetail)
            @include('sales._item_row', ['index' => $index, 'itemDetail' => $itemDetail, 'itemsList' => $itemsList])
        @endforeach
    @else
        {{-- لعرض صف واحد فارغ في وضع الإنشاء --}}
        @include('sales._item_row', ['index' => 0, 'itemDetail' => null, 'itemsList' => $itemsList])
    @endif
</div>

<div class="mb-3 text-left">
    <button type="button" class="btn btn-sm btn-info" id="add-sale-item-row">
        + إضافة صنف جديد
    </button>
</div>

<hr>
<h5 class="mb-3">ملخص الفاتورة والدفع</h5>

<div class="row" dir="rtl">
    {{-- Total Amount (Calculated Subtotals) --}}
    <div class="col-md-4 mb-3">
        <label for="total_amount" class="form-label">المبلغ الإجمالي (قبل الخصم) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount"
               value="{{ old('total_amount', $sale->total_amount ?? 0) }}" required readonly>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Discount Amount --}}
    <div class="col-md-4 mb-3">
        <label for="discount_amount" class="form-label">قيمة الخصم</label>
        <input type="number" step="0.01" min="0" class="form-control text-left @error('discount_amount') is-invalid @enderror" id="discount_amount" name="discount_amount"
               value="{{ old('discount_amount', $sale->discount_amount ?? 0) }}" placeholder="0.00">
        @error('discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Final Amount (Calculated Total - Discount) --}}
    <div class="col-md-4 mb-3">
        <label for="final_amount" class="form-label">المبلغ النهائي المستحق <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('final_amount') is-invalid @enderror" id="final_amount" name="final_amount"
               value="{{ old('final_amount', $sale->final_amount ?? 0) }}" required readonly>
        @error('final_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row" dir="rtl">
    {{-- Payment Method --}}
    <div class="col-md-6 mb-3">
        <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
        @php
            $selectedMethod = old('payment_method', $sale->payment_method ?? 'Cash');
        @endphp
        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
            <option value="Cash" {{ $selectedMethod == 'Cash' ? 'selected' : '' }}>نقداً</option>
            <option value="Credit Card" {{ $selectedMethod == 'Credit Card' ? 'selected' : '' }}>بطاقة ائتمانية</option>
            <option value="Bank Transfer" {{ $selectedMethod == 'Bank Transfer' ? 'selected' : '' }}>تحويل بنكي</option>
            <option value="Other" {{ $selectedMethod == 'Other' ? 'selected' : '' }}>أخرى</option>
        </select>
        @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes --}}
<div class="mb-3" dir="rtl">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $sale->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- ****************************************************** --}}
{{-- *************** JAVASCRIPT FOR CALCULATIONS ************** --}}
{{-- ****************************************************** --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
            
<script>
    // NOTE: هذا الكود يفترض أنك استخدمت jQuery كما ناقشنا سابقاً
    
    $(document).ready(function() {
        let nextIndex = {{ $sale ? $sale->items->count() : 1 }};

        // 1. حساب الإجمالي الفرعي لصف واحد
        function calculateSubTotal(row) {
            let quantity = parseFloat(row.find('.item-quantity').val()) || 0;
            let price = parseFloat(row.find('.item-price').val()) || 0;
            let subTotal = quantity * price;

            row.find('.item-subtotal').val(subTotal.toFixed(2));
            calculateTotalAmount();
        }

        // 2. حساب الإجمالي الكلي وقيمة الخصم
        function calculateTotalAmount() {
            let total = 0;

            $('.sale-item-row').each(function() {
                let subTotal = parseFloat($(this).find('.item-subtotal').val()) || 0;
                total += subTotal;
            });

            // تحديث المبلغ الإجمالي قبل الخصم
            $('#total_amount').val(total.toFixed(2));

            // تطبيق الخصم
            let discount = parseFloat($('#discount_amount').val()) || 0;
            let finalAmount = total - discount;

            // تحديث المبلغ النهائي
            $('#final_amount').val(finalAmount.toFixed(2));
        }

        // 3. إضافة صف جديد
        $('#add-sale-item-row').on('click', function() {
            const container = $('#sale-items-container');
            
            // استخدام قالب JS بسيط لصف جديد
            let newRow = $(`
                <div class="row sale-item-row mb-2" data-index="${nextIndex}" dir="rtl">
                    <div class="col-md-5">
                        <select class="form-select item-select" name="items[${nextIndex}][item_id]" required>
                            <option value="" disabled selected>اختر الصنف...</option>
                            @foreach($itemsList as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}">
                                    {{ $item->name }} (سعر البيع: {{ number_format($item->selling_price, 2) }} ر.س)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" class="form-control item-quantity text-left" name="items[${nextIndex}][quantity]" value="1" placeholder="الكمية" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" min="0.01" class="form-control item-price text-left" name="items[${nextIndex}][unit_price]" placeholder="سعر الوحدة" required>
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

        // 4. معالجة الأحداث (Quantity, Price, Discount)
        
        // عند تغيير الكمية أو السعر
        $('#sale-items-container').on('input', '.item-quantity, .item-price', function() {
            let row = $(this).closest('.sale-item-row');
            calculateSubTotal(row);
        });

        // عند تغيير قيمة الخصم
        $('#discount_amount').on('input', calculateTotalAmount);

        // عند اختيار صنف جديد (ملء سعر البيع الافتراضي)
        $('#sale-items-container').on('change', '.item-select', function() {
            let row = $(this).closest('.sale-item-row');
            let selectedOption = $(this).find(':selected');
            let defaultPrice = selectedOption.data('price') || 0;
            
            row.find('.item-price').val(defaultPrice.toFixed(2));
            calculateSubTotal(row);
        });

        // 5. حذف الصف
        $('#sale-items-container').on('click', '.remove-item-row', function() {
            $(this).closest('.sale-item-row').remove();
            calculateTotalAmount();
        });

        // تشغيل الحسابات الأولية عند تحميل الصفحة (للتعديل أو الإنشاء)
        $('.sale-item-row').each(function() {
             calculateSubTotal($(this));
        });
        calculateTotalAmount();
    });
</script>