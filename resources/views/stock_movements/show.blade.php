<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل حركة المخزون (#{{ $movement->id }})</h2>
        <div class="text-left">
            <a href="{{ route('stock_movements.index') }}" class="btn btn-secondary">العودة للسجل</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">بيانات الحركة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>تاريخ الحركة:</strong>
                    <p>{{ $movement->movement_date?->format('d F, Y') ?? 'لا يوجد تاريخ' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>الصنف:</strong>
                    <p>{{ $movement->item->name ?? 'صنف محذوف' }}</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <strong>نوع الحركة:</strong>
                    @if ($movement->movement_type == 'IN')
                        <p><span class="badge bg-success fs-6">زيادة المخزون</span></p>
                    @else
                        <p><span class="badge bg-danger fs-6">نقص المخزون</span></p>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <strong>الكمية المتغيرة:</strong>
                    <p class="h4">{{ $movement->quantity_change }} وحدة</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>المخزون بعد الحركة:</strong>
                    <p class="h4">{{ $movement->current_stock }} وحدة</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>سبب الحركة:</strong>
                    <p>{{ $movement->reason }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>مرجع العملية:</strong>
                    <p>{{ $movement->reference_type }} رقم #{{ $movement->reference_id }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>