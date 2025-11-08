<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4>تعديل بيانات الزبون: {{ $customer->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @include('customers._form', ['customer' => $customer])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">تحديث البيانات</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>