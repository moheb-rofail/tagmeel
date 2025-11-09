<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>سجلات الإرجاع</h2>
        <a href="{{ route('returns.create') }}" class="btn btn-primary">
            تسجيل عملية إرجاع جديدة
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-right">
                    <thead class="bg-light">
                        <tr>
                            <th>الرقم</th>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>المرجع</th>
                            <th>الطرف الآخر</th>
                            <th>المبلغ الإجمالي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $return)
                            <tr>
                                <td>{{ $return->id }}</td>
                                <td>{{ $return->return_date->format('d M, Y') }}</td>
                                <td>
                                    @if ($return->return_type == 'sale')
                                        <span class="badge bg-success">إرجاع مبيعات</span>
                                    @else
                                        <span class="badge bg-danger">إرجاع مشتريات</span>
                                    @endif
                                </td>
                                <td>
                                    @if($return->reference_id)
                                        {{ $return->return_type == 'sale' ? 'بيع #' : 'شراء #' }}{{ $return->reference_id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $return->customer_supplier_name ?? 'غير محدد' }}</td>
                                <td class="text-danger">{{ number_format($return->total_amount, 2) }} ج</td>
                                <td>
                                    <span class="badge bg-secondary">{{ __($return->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-info ms-1">عرض</a>
                                    <a href="{{ route('returns.edit', $return) }}" class="btn btn-sm btn-warning ms-1">تعديل</a>
                                    <form action="{{ route('returns.destroy', $return) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف سجل الإرجاع؟ سيؤدي هذا إلى عكس حركة المخزون.')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">لم يتم تسجيل أي إرجاعات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($returns->hasPages())
        <div class="card-footer">
            {{ $returns->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>