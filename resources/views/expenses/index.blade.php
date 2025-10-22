<x-layout>
<div class="container text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>قائمة المصروفات</h2>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            تسجيل مصروف جديد
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
                            <th>الوصف</th>
                            <th>الفئة</th>
                            <th>المبلغ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->id }}</td>
                                <td>{{ $expense->expense_date->format('d M, Y') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($expense->description, 50) }}</td>
                                <td>{{ $expense->category ?? 'غير مصنَّف' }}</td>
                                <td>{{ number_format($expense->amount, 2) }} ج.م</td>
                                <td>
                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info ms-1">عرض</a>
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning ms-1">تعديل</a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">لم يتم تسجيل أي مصروفات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($expenses->hasPages())
        <div class="card-footer">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>