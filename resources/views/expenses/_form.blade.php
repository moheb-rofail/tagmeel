{{-- Pass the $expense variable from the edit view, or initialize it as null for the create view --}}
@php
    $expense = $expense ?? null;
@endphp

<div class="row" dir="rtl">
    {{-- Expense Date --}}
    <div class="col-md-6 mb-3">
        <label for="expense_date" class="form-label">تاريخ المصروف <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date"
               value="{{ old('expense_date', $expense ? $expense->expense_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('expense_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Amount --}}
    <div class="col-md-6 mb-3">
        <label for="amount" class="form-label">المبلغ (ج.م) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control text-left @error('amount') is-invalid @enderror" id="amount" name="amount"
               value="{{ old('amount', $expense->amount ?? '') }}" placeholder="مثال: 125.50" required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Description --}}
<div class="mb-3" dir="rtl">
    <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description"
           value="{{ old('description', $expense->description ?? '') }}" placeholder="مثال: شراء لوازم مكتبية من شركة الرواد" required>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Category --}}
<div class="mb-3" dir="rtl">
    <label for="category" class="form-label">الفئة</label>
    <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category"
           value="{{ old('category', $expense->category ?? '') }}" placeholder="مثال: فواتير، سفر، برمجيات">
    @error('category')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Notes --}}
<div class="mb-3" dir="rtl">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $expense->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Receipt Upload --}}
<div class="mb-3" dir="rtl">
    <label for="receipt" class="form-label">الإيصال/الفاتورة (ملف PDF/صورة)</label>
    <input type="file" class="form-control @error('receipt') is-invalid @enderror" id="receipt" name="receipt">
    <small class="form-text text-muted">الحد الأقصى للحجم: 2 ميجابايت. التنسيقات المقبولة: pdf, jpg, jpeg, png.</small>
    @error('receipt')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if ($expense && $expense->receipt_path)
        <div class="mt-2">
            <span class="badge bg-success">يوجد إيصال/فاتورة حالية</span>
            <a href="{{ $expense->receipt_path }}" target="_blank" class="text-decoration-none me-2">عرض</a>
            <small class="text-warning d-block">رفع ملف جديد سيحل محل الملف الحالي.</small>
        </div>
    @endif
</div>