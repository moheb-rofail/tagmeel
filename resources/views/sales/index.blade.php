<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>قائمة المبيعات</h2>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            تسجيل عملية بيع جديدة
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
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
                            <th>العميل</th>
                            <th>المبلغ الإجمالي</th>
                            <th>المبلغ النهائي</th>
                            <th>طريقة الدفع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                                <td>{{ $sale->invoice_number ?? 'غير محدد' }}</td>
                                <td>{{ $sale->customer_name ?? 'عميل نقدي' }}</td>
                                <td>{{ number_format($sale->total_amount, 2) }} ج.م</td>
                                <td class="text-success fw-bold">{{ number_format($sale->final_amount, 2) }} ج.م</td>
                                <td>{{ $sale->payment_method }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info ms-1">عرض</a>
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-warning ms-1">تعديل</a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف عملية البيع هذه؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">لم يتم تسجيل أي مبيعات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($sales->hasPages())
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>