<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4>تسجيل مصروف جديد</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @include('expenses._form')

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">حفظ المصروف</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>