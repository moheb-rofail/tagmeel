<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>سجل حركة المخزون</h2>
    </div>

    <div class="alert alert-info" role="alert">
        هذا السجل يتم توليده تلقائيًا عند تسجيل المبيعات أو المشتريات أو الإرجاعات. لا يمكن إضافة حركات يدوياً.
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-right">
                    <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>الصنف</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>المخزون بعد الحركة</th>
                            <th>السبب</th>
                            <th>المرجع</th>
                            <th>عرض</th>
                            <th>سجل الصنف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td>{{ $movement->movement_date->format('d M, Y') }}</td>
                                <td>{{ $movement->item->name ?? 'صنف محذوف' }}</td>
                                <td>
                                    @if ($movement->movement_type == 'IN')
                                        <span class="badge bg-success">زيادة (+{{ $movement->quantity_change }})</span>
                                    @else
                                        <span class="badge bg-danger">نقص (-{{ $movement->quantity_change }})</span>
                                    @endif
                                </td>
                                <td>{{ $movement->quantity_change }}</td>
                                <td>{{ $movement->current_stock }}</td>
                                <td>{{ $movement->reason }}</td>
                                <td>
                                    {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                </td>
                                <td>
                                    <a href="{{ route('stock_movements.show', $movement) }}" class="btn btn-sm btn-info">عرض</a>
                                </td>
                                <td><a href="{{ route('stock_movements.item_history', $movement->item) }}" class="btn btn-sm btn-info">سجل الصنف</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">لا توجد حركات مخزون مسجلة بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($movements->hasPages())
        <div class="card-footer">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>