<x-layout>
<div class="container text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل الزبون: {{ $customer->name }}</h2>
        <div class="text-left">
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning ms-2">تعديل</a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">بيانات الاتصال</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>الاسم الكامل:</strong>
                    <p>{{ $customer->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>رقم الهاتف:</strong>
                    <p>{{ $customer->phone ?? 'لا يوجد' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>العنوان:</strong>
                    <p>{{ $customer->address ?? 'لا يوجد عنوان' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">سجل الحساب والأرصدة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>الرصيد الافتتاحي:</strong>
                    <p>{{ number_format($customer->initial_balance, 2) }} ج</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>نوع الرصيد الافتتاحي:</strong>
                    <p><span class="badge bg-secondary">{{ $customer->balance_type == 'Debit' ? 'مدين (عليه)' : 'دائن (له)' }}</span></p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>الرصيد الحالي:</strong>
                    <p class="h4 {{ $customer->current_balance > 0 ? ($customer->balance_type == 'Debit' ? 'text-danger' : 'text-success') : 'text-secondary' }}">
                        {{ number_format(abs($customer->current_balance), 2) }} ج
                        @if($customer->current_balance != 0)
                            <span><a class="btn btn-success" href="{{ route('customer_payments.create', $customer->id) }}">سداد مديونية</a></span>
                        @endif
                    </p>
                    <p class="mt-2">
                        @if ($customer->current_balance == 0)
                            <span class="badge bg-dark">الرصيد صفر</span>
                        @elseif ($customer->balance_type == 'Debit')
                            <span class="badge bg-danger">**الزبون مدين لك**</span>
                        @else
                            <span class="badge bg-success">**الزبون دائن لك**</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>ملاحظات:</strong>
                    <p>{{ $customer->notes ?? 'لا يوجد ملاحظات.' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ************************************************* --}}
    {{-- ** جدول سجل السداد (Customer Payments) ** --}}
    {{-- ************************************************* --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">سجل المدفوعات والسدادات</h5>
        </div>
        <div class="card-body p-0">
            @if ($customer->payments->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped mb-0 text-right">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>المبلغ المسدد</th>
                            <th>طريقة الدفع</th>
                            <th>الرصيد السابق</th>
                            <th>الرصيد بعد السداد</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date?->format('d M, Y') ?? 'N/A' }}</td>
                            <td class="text-success fw-bold">{{ number_format($payment->amount, 2) }} ر.س</td>
                            <td>{{ $payment->payment_method ?? '-' }}</td>
                            <td>{{ number_format($payment->previous_balance, 2) }}</td>
                            <td>{{ number_format($payment->new_balance, 2) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($payment->notes, 40) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="p-3 text-center">لا يوجد سجل مدفوعات لهذا العميل بعد.</p>
            @endif
        </div>
    </div>
    
    {{-- هنا يمكن إضافة جدول معاملات المبيعات الآجلة لهذا العميل --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">سجل المبيعات الآجلة</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-right">
                    <thead class="bg-light">
                        <tr>
                            <th>الرقم</th>
                            <th>التاريخ</th>
                            <th>رقم الفاتورة</th>
                            <th>المبلغ الإجمالي</th>
                            <th>المبلغ النهائي</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                                <td>{{ $sale->invoice_number ?? 'غير محدد' }}</td>
                                <td>{{ number_format($sale->total_amount, 2) }} ج.م</td>
                                <td class="text-success fw-bold">{{ number_format($sale->final_amount, 2) }} ج.م</td>
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
        
        
    </div>
    
</div>
</x-layout>