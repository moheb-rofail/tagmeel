<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل فاتورة الشراء (#{{ $purchase->id }})</h2>
        <div class="text-left">
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning ms-2">تعديل</a>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">معلومات الفاتورة الأساسية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>المورد:</strong>
                    <p>{{ $purchase->supplier->name ?? 'غير متوفر' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>تاريخ الشراء:</strong>
                    <p>{{ $purchase->purchase_date->format('d F, Y') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>رقم الفاتورة:</strong>
                    <p>{{ $purchase->invoice_number ?? 'غير محدد' }}</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <strong>المبلغ الإجمالي:</strong>
                    <p class="h4 text-danger">{{ number_format($purchase->total_amount, 2) }} ج.م</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>حالة الدفع:</strong>
                    @php
                        $statusClass = [
                            'Paid' => 'badge bg-success',
                            'Pending' => 'badge bg-warning text-dark',
                            'Partial' => 'badge bg-info',
                        ][$purchase->status] ?? 'badge bg-secondary';
                    @endphp
                    <p><span class="{{ $statusClass }} fs-6">{{ __($purchase->status) }}</span></p>
                </div>
                
                <div class="col-md-12 mb-3">
                    <strong>ملاحظات:</strong>
                    <p>{{ $purchase->notes ?? 'لا توجد ملاحظات.' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- تفاصيل الأصناف المشتراة --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">تفاصيل الأصناف</h5>
        </div>
        <div class="card-body p-0">
            @if($purchase->items->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped mb-0 text-right">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th>رمز الصنف</th>
                            <th>الكمية</th>
                            <th>تكلفة الوحدة</th>
                            <th>الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $itemDetail)
                        <tr>
                            <td>{{ $itemDetail->item->name ?? 'صنف محذوف' }}</td>
                            <td>{{ $itemDetail->item->id ?? 'N/A' }}</td>
                            <td>{{ $itemDetail->quantity }}</td>
                            <td>{{ number_format($itemDetail->unit_cost, 2) }} ج.م</td>
                            <td>{{ number_format($itemDetail->sub_total, 2) }} ج.م</td>
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