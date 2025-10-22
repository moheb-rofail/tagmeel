<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>تسجيل عملية بيع جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        {{-- تمرير الأصناف المتاحة لـ _form --}}
                        @include('sales._form', ['itemsList' => $items])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">حفظ عملية البيع</button>
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>