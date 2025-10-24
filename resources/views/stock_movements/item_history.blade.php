<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>سجل حركة المخزون للصنف: **{{ $item->name }}**</h2>
        <div class="text-left">
            <a href="{{ route('stock_movements.index') }}" class="btn btn-secondary">العودة لسجل المخزون العام</a>
        </div>
    </div>

    <div class="alert alert-info" role="alert">
        هذا السجل يوضح الزيادة والنقصان في مخزون الصنف **{{ $item->name }}** الحالي ({{ $item->stock_quantity }} وحدة).
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-right">
                    <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>المخزون بعد الحركة</th>
                            <th>السبب</th>
                            <th>المرجع</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td>{{ $movement->movement_date?->format('d M, Y') ?? 'N/A' }}</td>
                                <td>
                                    @if ($movement->movement_type == 'IN')
                                        <span class="badge bg-success">زيادة (+{{ $movement->quantity_change }})</span>
                                    @else
                                        <span class="badge bg-danger">نقص (-{{ $movement->quantity_change }})</span>
                                    @endif
                                </td>
                                <td>{{ $movement->quantity_change }}</td>
                                <td>**{{ $movement->current_stock }}**</td>
                                <td>{{ $movement->reason }}</td>
                                <td>
                                    {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                </td>
                                <td>
                                    <a href="{{ route('stock_movements.show', $movement) }}" class="btn btn-sm btn-info">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لا توجد حركات مسجلة لهذا الصنف بعد.</td>
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