<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل الإرجاع (#{{ $return->id }})</h2>
        <div class="text-left">
            <a href="{{ route('returns.edit', $return) }}" class="btn btn-warning ms-2">تعديل</a>
            <a href="{{ route('returns.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">معلومات الإرجاع الأساسية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>تاريخ الإرجاع:</strong>
                    <p>{{ $return->return_date->format('d F, Y') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>النوع:</strong>
                    @if ($return->return_type == 'sale')
                        <p><span class="badge bg-success fs-6">إرجاع مبيعات (مخزون داخل)</span></p>
                    @else
                        <p><span class="badge bg-danger fs-6">إرجاع مشتريات (مخزون خارج)</span></p>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <strong>المرجع:</strong>
                    @if ($return->reference_id)
                        <p>{{ $return->return_type == 'sale' ? 'بيع #' : 'شراء #' }}{{ $return->reference_id }}</p>
                    @else
                        <p>لا يوجد مرجع</p>
                    @endif
                </div>
                
                <div class="col-md-4 mb-3">
                    <strong>الطرف المعني:</strong>
                    <p>{{ $return->customer_supplier_name ?? 'غير محدد' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>المبلغ المعكوس:</strong>
                    <p class="h4 text-danger">{{ number_format($return->total_amount, 2) }} ج</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>الحالة:</strong>
                    <p><span class="badge bg-secondary fs-6">{{ __($return->status) }}</span></p>
                </div>
                
                <div class="col-md-12 mb-3">
                    <strong>سبب الإرجاع:</strong>
                    <p>{{ $return->reason ?? 'لا يوجد سبب محدد.' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- تفاصيل الأصناف المرجعة --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">تفاصيل الأصناف المرجعة</h5>
        </div>
        <div class="card-body p-0">
            @if($return->items->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped mb-0 text-right">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th>الكمية المرجعة</th>
                            <th>قيمة الوحدة</th>
                            <th>الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $itemDetail)
                        <tr>
                            <td>{{ $itemDetail->item->name ?? 'صنف محذوف' }}</td>
                            <td>{{ $itemDetail->quantity }}</td>
                            <td>{{ number_format($itemDetail->unit_value, 2) }} ج</td>
                            <td>{{ number_format($itemDetail->sub_total, 2) }} ج</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="p-3 text-center">لا توجد تفاصيل أصناف لهذا الإرجاع.</p>
            @endif
        </div>
    </div>
</div>
</x-layout>