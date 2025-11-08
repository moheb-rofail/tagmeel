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
                    <p>{{ number_format($customer->initial_balance, 2) }} ر.س</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>نوع الرصيد الافتتاحي:</strong>
                    <p><span class="badge bg-secondary">{{ $customer->balance_type == 'Debit' ? 'مدين (عليه)' : 'دائن (له)' }}</span></p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>الرصيد الحالي:</strong>
                    <p class="h4 {{ $customer->current_balance > 0 ? ($customer->balance_type == 'Debit' ? 'text-danger' : 'text-success') : 'text-secondary' }}">
                        {{ number_format(abs($customer->current_balance), 2) }} ر.س
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
    
    {{-- هنا يمكن إضافة جدول معاملات المبيعات الآجلة لهذا العميل --}}
    {{-- <h4>سجل المبيعات الآجلة</h4> --}}
    
</div>
</x-layout>