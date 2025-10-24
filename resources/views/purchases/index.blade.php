<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>قائمة فواتير المشتريات</h2>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            تسجيل فاتورة شراء جديدة
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-right">
                    <thead class="bg-light">
                        <tr>
                            <th>الرقم</th>
                            <th>التاريخ</th>
                            <th>رقم الفاتورة</th>
                            <th>المورد</th>
                            <th>المبلغ الإجمالي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
                                <td>{{ $purchase->invoice_number ?? 'غير محدد' }}</td>
                                <td>{{ $purchase->supplier->name ?? 'مورد محذوف' }}</td>
                                <td>{{ number_format($purchase->total_amount, 2) }} ج.م</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'Paid' => 'badge bg-success',
                                            'Pending' => 'badge bg-warning text-dark',
                                            'Partial' => 'badge bg-info',
                                        ][$purchase->status] ?? 'badge bg-secondary';
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ __($purchase->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info ms-1">عرض</a>
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning ms-1">تعديل</a>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف فاتورة الشراء هذه؟ سيؤدي هذا إلى عكس الكميات في المخزون.')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم تسجيل أي فواتير مشتريات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($purchases->hasPages())
        <div class="card-footer">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>