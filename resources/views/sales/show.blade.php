<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل عملية البيع (#{{ $sale->id }})</h2>
        <div class="text-left">
            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning ms-2">تعديل</a>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">معلومات الفاتورة الأساسية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><strong>تاريخ البيع:</strong><p>{{ $sale->sale_date->format('d F, Y') }}</p></div>
                <div class="col-md-4 mb-3"><strong>رقم الفاتورة:</strong><p>{{ $sale->invoice_number ?? 'غير محدد' }}</p></div>
                <div class="col-md-4 mb-3"><strong>العميل:</strong><p>{{ $sale->customer_name ?? 'عميل نقدي' }}</p></div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>المبلغ الإجمالي (قبل الخصم):</strong>
                    <p>{{ number_format($sale->total_amount, 2) }} ج</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>الخصم:</strong>
                    <p class="text-danger">{{ number_format($sale->discount_amount, 2) }} ج</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>المبلغ النهائي المستحق:</strong>
                    <p class="h4 text-success">{{ number_format($sale->final_amount, 2) }} ج</p>
                </div>
                
                <div class="col-md-12 mb-3">
                    <strong>ملاحظات:</strong>
                    <p>{{ $sale->notes ?? 'لا توجد ملاحظات.' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- تفاصيل الأصناف المباعة --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">تفاصيل الأصناف المباعة</h5>
        </div>
        <div class="card-body p-0">
            @if($sale->items->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped mb-0 text-right">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th>سعر البيع الافتراضي</th>
                            <th>الكمية</th>
                            <th>سعر الوحدة المباع</th>
                            <th>الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $itemDetail)
                        <tr>
                            <td>{{ $itemDetail->item->name ?? 'صنف محذوف' }}</td>
                            <td>{{ number_format($itemDetail->item->selling_price ?? 0, 2) }} ج</td>
                            <td>{{ $itemDetail->quantity }}</td>
                            <td>{{ number_format($itemDetail->unit_price, 2) }} ج</td>
                            <td>{{ number_format($itemDetail->sub_total, 2) }} ج</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="p-3 text-center">لا توجد تفاصيل أصناف لهذه الفاتورة.</p>
            @endif
        </div>
    </div>
</div>
</x-layout>