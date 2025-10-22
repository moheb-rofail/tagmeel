<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4>تعديل عملية البيع: #{{ $sale->id }} ({{ $sale->invoice_number ?? 'غير محدد' }})</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.update', $sale) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- تمرير الفاتورة والأصناف المتاحة لـ _form --}}
                        @include('sales._form', ['sale' => $sale, 'itemsList' => $items])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">تحديث عملية البيع</button>
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>