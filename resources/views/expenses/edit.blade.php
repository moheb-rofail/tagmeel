<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4>تعديل المصروف: #{{ $expense->id }} ({{ $expense->description }})</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('expenses._form', ['expense' => $expense])

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">تحديث المصروف</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layout>