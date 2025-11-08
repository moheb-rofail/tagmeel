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
        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number"
            name="invoice_number" value="{{ old('invoice_number', $sale->invoice_number ?? '') }}"
            placeholder="رقم الفاتورة (اختياري)">
        @error('invoice_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Customer Name --}}

    <div class="col-md-4 mb-3">
        <label for="customer_select" class="form-label">العميل</label>
        <div class="input-group">
            <select class="form-select" id="customer_select" name="customer_id">
                <option value="">عميل نقدي</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ (old('customer_id', $sale->customer_id ?? '') == $customer->id) ? 'selected' : '' }} data-name="{{ $customer->name }}">
                        {{ $customer->name }}
                        @if($customer->current_balance != 0)
                            (الرصيد: {{ $customer->current_balance }}
                            {{ $customer->balance_type == 'Debit' ? 'مدين' : 'دائن' }})
                        @endif
                    </option>
                @endforeach
            </select>
            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name"
                name="customer_name" value="{{ old('customer_name', $sale->customer_name ?? 'عميل نقدي') }}" readonly>
        </div>
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
        <label for="total_amount" class="form-label">المبلغ الإجمالي (قبل الخصم) <span
                class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('total_amount') is-invalid @enderror"
            id="total_amount" name="total_amount" value="{{ old('total_amount', $sale->total_amount ?? 0) }}" required
            readonly>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Discount Amount --}}
    <div class="col-md-4 mb-3">
        <label for="discount_amount" class="form-label">قيمة الخصم</label>
        <input type="number" step="0.01" min="0"
            class="form-control text-left @error('discount_amount') is-invalid @enderror" id="discount_amount"
            name="discount_amount" value="{{ old('discount_amount', $sale->discount_amount ?? 0) }}" placeholder="0.00">
        @error('discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Final Amount (Calculated Total - Discount) --}}
    <div class="col-md-4 mb-3">
        <label for="final_amount" class="form-label">المبلغ النهائي المستحق <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('final_amount') is-invalid @enderror"
            id="final_amount" name="final_amount" value="{{ old('final_amount', $sale->final_amount ?? 0) }}" required
            readonly>
        @error('final_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row" dir="rtl">

    {{-- Payment Status --}}
    <div class="col-md-4 mb-3">
        <label for="payment_status" class="form-label">حالة الدفع <span class="text-danger">*</span></label>
        @php
            $selectedStatus = old('payment_status', $sale->payment_status ?? 'Not Paid');
        @endphp
        <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status"
            name="payment_status">
            <option value="Not Paid" {{ $selectedStatus === 'Not Paid' ? 'selected' : '' }}>غير مدفوع</option>
            <option value="Partial" {{ $selectedStatus === 'Partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
            <option value="Paid" {{ $selectedStatus === 'Paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
        </select>
        @error('payment_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Paid Amount --}}
    <div class="col-md-4 mb-3">
        <label for="paid_amount" class="form-label">المبلغ المدفوع</label>
        <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount"
            class="form-control text-left @error('paid_amount') is-invalid @enderror"
            value="{{ old('paid_amount', $sale->paid_amount ?? 0) }}">
        @error('paid_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes --}}
<div class="mb-3" dir="rtl">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
        rows="3">{{ old('notes', $sale->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- ****************************************************** --}}
{{-- *************** JAVASCRIPT FOR CALCULATIONS ************** --}}
{{-- ****************************************************** --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
class SaleForm {
    constructor() {
        this.nextIndex = {{ $sale ? $sale->items->count() : 1 }};
        this.form = $('#saleForm');
        this.itemsContainer = $('#sale-items-container');
        this.initializeEventListeners();
        this.initializeForm();
    }

    initializeEventListeners() {
        // Items container events
        this.itemsContainer.on('change', '.item-select', (e) => this.handleItemSelect(e));
        this.itemsContainer.on('input', '.item-quantity, .item-price', (e) => this.handleQuantityPriceChange(e));
        this.itemsContainer.on('click', '.remove-item-row', (e) => this.handleRemoveRow(e));

        // Form-wide events
        $('#add-sale-item-row').on('click', () => this.addNewRow());
        $('#discount_amount').on('input', () => this.calculateTotalAmount());
        $('#payment_status').on('change', () => this.syncPaidAmountVisibility());
        $('#customer_select').on('change', (e) => this.handleCustomerChange(e));
        
        // Form submission
        this.form.on('submit', (e) => this.handleSubmit(e));
    }

    initializeForm() {
        $('.sale-item-row').each((_, row) => this.calculateSubTotal($(row)));
        this.calculateTotalAmount();
        this.syncPaidAmountVisibility();
    }

    calculateSubTotal(row) {
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const subTotal = quantity * price;
        row.find('.item-subtotal').val(subTotal.toFixed(2));
        this.calculateTotalAmount();
    }

    calculateTotalAmount() {
        const total = Array.from($('.sale-item-row'))
            .reduce((sum, row) => sum + (parseFloat($(row).find('.item-subtotal').val()) || 0), 0);
        
        const discount = parseFloat($('#discount_amount').val()) || 0;
        const finalAmount = Math.max(0, total - discount);

        $('#total_amount').val(total.toFixed(2));
        $('#final_amount').val(finalAmount.toFixed(2));

        if ($('#payment_status').val() === 'Paid') {
            $('#paid_amount').val(finalAmount.toFixed(2));
        }
    }

    syncPaidAmountVisibility() {
        const status = $('#payment_status').val();
        const finalAmount = parseFloat($('#final_amount').val()) || 0;
        const selectedCustomerId = $('#customer_select').val();

        switch(status) {
            case 'Not Paid':
                $('#paid_amount').val(0).prop('readonly', true);
                this.toggleBalanceField(!!selectedCustomerId);
                break;
            case 'Paid':
                $('#paid_amount').val(finalAmount.toFixed(2)).prop('readonly', true);
                this.toggleBalanceField(false);
                break;
            case 'Partial':
                $('#paid_amount').prop('readonly', false);
                this.toggleBalanceField(false);
                break;
        }
    }

    toggleBalanceField(add) {
        $('#add_to_balance').remove();
        if (add) {
            this.form.append('<input type="hidden" id="add_to_balance" name="add_to_balance" value="1">');
        }
    }

    handleItemSelect(event) {
        const row = $(event.target).closest('.sale-item-row');
        const price = $(event.target).find('option:selected').data('price') || 0;
        row.find('.item-price').val(price);
        this.calculateSubTotal(row);
    }

    handleQuantityPriceChange(event) {
        this.calculateSubTotal($(event.target).closest('.sale-item-row'));
    }

    handleRemoveRow(event) {
        $(event.target).closest('.sale-item-row').remove();
        this.calculateTotalAmount();
    }

    handleCustomerChange(event) {
        const selectedOption = $(event.target).find('option:selected');
        const customerName = selectedOption.data('name') || 'عميل نقدي';
        $('#customer_name').val(customerName);
        this.syncPaidAmountVisibility();
    }

    addNewRow() {
        const template = `
            <div class="row sale-item-row mb-2" data-index="${this.nextIndex}" dir="rtl">
                <div class="col-md-5">
                    <select class="form-select item-select" name="items[${this.nextIndex}][item_id]" required>
                        <option value="">اختر الصنف...</option>
                        @foreach($itemsList as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}">
                                {{ $item->name }} ({{ number_format($item->selling_price, 2) }} ر.س)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" min="1" class="form-control item-quantity text-left" 
                           name="items[${this.nextIndex}][quantity]" value="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0.01" class="form-control item-price text-left" 
                           name="items[${this.nextIndex}][unit_price]" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control item-subtotal text-left" 
                           name="items[${this.nextIndex}][sub_total]" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item-row">×</button>
                </div>
            </div>
        `;
        
        this.itemsContainer.append(template);
        this.nextIndex++;
    }

    handleSubmit(event) {
        event.preventDefault();
        
        if (!this.validateForm()) {
            return false;
        }

        event.target.submit();
    }

    validateForm() {
        if (this.itemsContainer.find('.sale-item-row').length === 0) {
            alert('يجب إضافة صنف واحد على الأقل');
            return false;
        }

        let isValid = true;
        this.form.find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            alert('يرجى ملء جميع الحقول المطلوبة');
            return false;
        }

        if (parseFloat($('#final_amount').val()) <= 0) {
            alert('المبلغ النهائي يجب أن يكون أكبر من صفر');
            return false;
        }

        return true;
    }
}

// Initialize the form when document is ready
$(document).ready(() => new SaleForm());
</script>