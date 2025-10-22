<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>تسجيل فاتورة شراء جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        
                        {{-- تمرير الموردين والأصناف لـ _form --}}
                        @include('purchases._form', ['suppliers' => $suppliers, 'items' => $items])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">حفظ فاتورة الشراء</button>
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>