{{-- Pass the $purchase, $suppliers, and $items variables safely --}}
@php
    $purchase = $purchase ?? null;
    $selectedSupplier = old('supplier_id', $purchase->supplier_id ?? null);
@endphp

<div class="row" dir="rtl">
    {{-- Supplier ID --}}
    <div class="col-md-4 mb-3">
        <label for="supplier_id" class="form-label">المورد <span class="text-danger">*</span></label>
        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id"
            required>
            <option value="" disabled {{ is_null($selectedSupplier) ? 'selected' : '' }}>اختر المورد...</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ $selectedSupplier == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Purchase Date --}}
    <div class="col-md-4 mb-3">
        <label for="purchase_date" class="form-label">تاريخ الشراء <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date"
            name="purchase_date"
            value="{{ old('purchase_date', $purchase ? $purchase->purchase_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            required>
        @error('purchase_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Invoice Number --}}
    <div class="col-md-4 mb-3">
        <label for="invoice_number" class="form-label">رقم فاتورة المورد</label>
        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number"
            name="invoice_number" value="{{ old('invoice_number', $purchase->invoice_number ?? '') }}"
            placeholder="رقم فاتورة المورد">
        @error('invoice_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>
<h5 class="mb-3">تفاصيل الأصناف المشترّاة (Purchase Items)</h5>

<div id="purchase-items-container">
    {{-- هنا سيتم توليد حقول الأصناف ديناميكياً بواسطة JavaScript --}}

    {{-- مثال على صنف (للمبتدئين) - يجب تكراره بواسطة JS: --}}
    @if ($purchase && $purchase->items->isNotEmpty())
        @foreach($purchase->items as $index => $itemDetail)
            @include('purchases._item_row', ['index' => $index, 'itemDetail' => $itemDetail, 'itemsList' => $items])
        @endforeach
    @else
        {{-- لعرض صف واحد فارغ في وضع الإنشاء --}}
        @include('purchases._item_row', ['index' => 0, 'itemDetail' => null, 'itemsList' => $items])
    @endif
</div>

<div class="mb-3 text-left">
    <button type="button" class="btn btn-sm btn-info" id="add-item-row">
        + إضافة صنف جديد
    </button>
</div>

<hr>
<h5 class="mb-3">ملخص الفاتورة</h5>

<div class="row" dir="rtl">
    {{-- Total Amount --}}
    <div class="col-md-4 mb-3">
        <label for="total_amount" class="form-label">المبلغ الإجمالي للفاتورة <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('total_amount') is-invalid @enderror"
            id="total_amount" name="total_amount" value="{{ old('total_amount', $purchase->total_amount ?? '') }}"
            required readonly> {{-- يفضل أن يكون للقراءة فقط (Readonly) ويُحسب بـ JS --}}
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label">حالة الدفع <span class="text-danger">*</span></label>
        @php
            $selectedStatus = old('status', $purchase->status ?? 'Pending');
        @endphp
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="Pending" {{ $selectedStatus == 'Pending' ? 'selected' : '' }}>معلّق</option>
            <option value="Paid" {{ $selectedStatus == 'Paid' ? 'selected' : '' }}>مدفوع</option>
            <option value="Partial" {{ $selectedStatus == 'Partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes --}}
<div class="mb-3" dir="rtl">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
        rows="3">{{ old('notes', $purchase->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>

{{-- ** ملاحظة: لكي يعمل هذا النموذج بشكل كامل، تحتاج إلى تضمين _item_row.blade.php وبعض JavaScript في الأسفل ** --}}
<script>
    // يجب إضافة منطق JavaScript هنا لحساب الإجمالي وإدارة صفوف الأصناف

    // ******************************************************
        // NOTE: هذا الكود يفترض أنك تستخدم مكتبة jQuery ($)
        // ******************************************************

        $(document).ready(function() {
            let nextIndex = {{ $purchase ? $purchase->items->count() : 1 }};

        // 1. حساب الإجمالي الفرعي لصف واحد
        function calculateSubTotal(row) {
            // البحث عن حقول الكمية والتكلفة والإجمالي الفرعي ضمن الصف الحالي
            let quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        let cost = parseFloat(row.find('.item-cost').val()) || 0;
        let subTotal = quantity * cost;

        // تحديث حقل الإجمالي الفرعي
        row.find('.item-subtotal').val(subTotal.toFixed(2));

        // تحديث الإجمالي الكلي بعد تحديث الإجمالي الفرعي
        calculateTotalAmount();
        }

        // 2. حساب الإجمالي الكلي لجميع الفواتير الفرعية
        function calculateTotalAmount() {
            let total = 0;

        // المرور على جميع صفوف الأصناف المضافة حالياً
        $('.purchase-item-row').each(function() {
            let subTotal = parseFloat($(this).find('.item-subtotal').val()) || 0;
        total += subTotal;
            });

        // تحديث حقل المبلغ الإجمالي
        $('#total_amount').val(total.toFixed(2));
        }

        // 3. إضافة صف جديد (إذا كنت تستخدم صفوفًا ديناميكية)
        $('#add-item-row').on('click', function() {
            const container = $('#purchase-items-container');

        // استنساخ الصف الفارغ أو استخدام قالب Blade (الأفضل استخدام AJAX/JS template)
        // لتبسيط المثال: سنستخدم هنا منطقًا بسيطًا

        let newRow = $(`
        <div class="row purchase-item-row mb-2" data-index="${nextIndex}" dir="rtl">
            <div class="col-md-5">
                <select class="form-select item-select" name="items[${nextIndex}][item_id]" required>
                    <option value="" disabled selected>اختر الصنف...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-cost="{{ $item->unit_price }}">
                                        {{ $item->name }} (سعر الوحدة الحالي: {{ number_format($item->unit_price, 2) }} ج.م)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" min="1" class="form-control item-quantity text-left" name="items[${nextIndex}][quantity]" value="1" placeholder="الكمية" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" min="0.01" class="form-control item-cost text-left" name="items[${nextIndex}][unit_cost]" placeholder="تكلفة الوحدة" required>
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

        // 4. معالجة الأحداث على الحقول المتغيرة (Quantity, Cost, Item Select)

        // عند تغيير أي حقل كمية أو تكلفة في أي صف
        $('#purchase-items-container').on('input', '.item-quantity, .item-cost', function() {
            let row = $(this).closest('.purchase-item-row');
            calculateSubTotal(row);
        });

        // عند اختيار صنف جديد، يتم ملء تكلفة الوحدة تلقائياً
        $('#purchase-items-container').on('change', '.item-select', function() {
            let row = $(this).closest('.purchase-item-row');
            let selectedOption = $(this).find(':selected');
            let defaultCost = selectedOption.data('cost') || 0;

            // ضع تكلفة الوحدة في حقل التكلفة
            row.find('.item-cost').val(defaultCost.toFixed(2));

            // احسب الإجمالي الفرعي
            calculateSubTotal(row);
        });

        // 5. حذف الصف
        $('#purchase-items-container').on('click', '.remove-item-row', function() {
            $(this).closest('.purchase-item-row').remove();
            calculateTotalAmount(); // إعادة حساب الإجمالي الكلي بعد الحذف
        });

        // تشغيل الحسابات الأولية عند تحميل الصفحة (لتعديل البيانات الموجودة)
        $('.purchase-item-row').each(function() {
            calculateSubTotal($(this));
        });
        calculateTotalAmount();
    });
</script>
