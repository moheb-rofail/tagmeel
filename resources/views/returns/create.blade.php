<x-layout>
<div class="container-xl text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>تسجيل عملية إرجاع جديدة</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('returns.store') }}" method="POST">
                        @csrf
                        
                        @include('returns._form', [
                            'itemsList' => $items,
                            'salesList' => $sales,
                            'purchasesList' => $purchases
                        ])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">حفظ سجل الإرجاع</button>
                            <a href="{{ route('returns.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>