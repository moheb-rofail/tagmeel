<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4>تعديل فاتورة الشراء: #{{ $purchase->id }} ({{ $purchase->invoice_number ?? 'غير محدد' }})</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- تمرير الفاتورة والموردين والأصناف لـ _form --}}
                        @include('purchases._form', ['purchase' => $purchase, 'suppliers' => $suppliers, 'items' => $items])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">تحديث الفاتورة</button>
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>