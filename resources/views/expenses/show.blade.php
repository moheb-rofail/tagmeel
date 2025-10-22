<x-layout>
<div class="container text-right" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل المصروف (#{{ $expense->id }})</h2>
        <div class="text-left">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning ms-2">تعديل</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">معلومات الحركة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>التاريخ:</strong>
                    <p>{{ $expense->expense_date->format('d F, Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>المبلغ:</strong>
                    <p class="h4 text-danger">{{ number_format($expense->amount, 2) }} ج.م</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>الوصف:</strong>
                    <p>{{ $expense->description }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>الفئة:</strong>
                    <p>{{ $expense->category ?? 'غير مصنَّف' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>ملاحظات:</strong>
                    <p>{{ $expense->notes ?? 'لا توجد ملاحظات.' }}</p>
                </div>

                @if ($expense->receipt_path)
                    <div class="col-md-12 mb-3">
                        <strong>الإيصال/الفاتورة:</strong>
                        <p>
                            <a href="{{ $expense->receipt_path }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض الإيصال</a>
                        </p>
                    </div>
                @endif

                <div class="col-md-6 mb-3">
                    <strong>تاريخ الإنشاء:</strong>
                    <p>{{ $expense->created_at->diffForHumans() }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>آخر تحديث:</strong>
                    <p>{{ $expense->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>