<x-layout>
<div class="container text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>سجل البائعين/الزبائن</h2>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            + إضافة زبون جديد
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
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>الرصيد الحالي</th>
                            <th>نوع الرصيد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td class="{{ $customer->current_balance > 0 ? ($customer->balance_type == 'Debit' ? 'text-danger' : 'text-success') : '' }}">
                                    {{ number_format($customer->current_balance, 2) }} ر.س
                                </td>
                                <td>
                                    @if ($customer->current_balance == 0)
                                        <span class="badge bg-secondary">صفر</span>
                                    @elseif ($customer->balance_type == 'Debit')
                                        <span class="badge bg-danger">مدين (عليه)</span>
                                    @else
                                        <span class="badge bg-success">دائن (له)</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info ms-1">عرض</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning ms-1">تعديل</a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الزبون؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">لم يتم تسجيل أي زبائن بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($customers->hasPages())
        <div class="card-footer">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
</x-layout>