{{-- يجب تمرير $customer بشكل اختياري في حالتي Create و Edit --}}
@php
    $customer = $customer ?? null;
@endphp

<div class="row" dir="rtl">
    {{-- Name --}}
    <div class="col-md-12 mb-3">
        <label for="name" class="form-label">اسم البائع/الزبون <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
               value="{{ old('name', $customer->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Phone --}}
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">رقم الهاتف</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
               value="{{ old('phone', $customer->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Address --}}
    <div class="col-md-6 mb-3">
        <label for="address" class="form-label">العنوان</label>
        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address"
               value="{{ old('address', $customer->address ?? '') }}">
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <hr class="mt-3 mb-3">
    
    {{-- Initial Balance (الرصيد الافتتاحي) --}}
    <div class="col-md-6 mb-3">
        <label for="initial_balance" class="form-label">الرصيد الافتتاحي (المبلغ)</label>
        <input type="number" step="0.01" min="0" class="form-control text-left @error('initial_balance') is-invalid @enderror" 
               id="initial_balance" name="initial_balance"
               value="{{ old('initial_balance', $customer->initial_balance ?? 0) }}" {{ $customer ? 'readonly' : '' }}>
        @error('initial_balance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($customer)
            <small class="form-text text-muted">لا يُسمح بتعديل الرصيد الافتتاحي بعد الإنشاء. يتم تعديل الرصيد الحالي عبر الفواتير.</small>
        @endif
    </div>

    {{-- Balance Type --}}
    <div class="col-md-6 mb-3">
        <label for="balance_type" class="form-label">نوع الرصيد <span class="text-danger">*</span></label>
        @php
            $selectedType = old('balance_type', $customer->balance_type ?? 'Debit');
        @endphp
        <select class="form-select @error('balance_type') is-invalid @enderror" id="balance_type" name="balance_type" required {{ $customer ? 'disabled' : '' }}>
            <option value="Debit" {{ $selectedType == 'Debit' ? 'selected' : '' }}>مدين (عليه دين)</option>
            <option value="Credit" {{ $selectedType == 'Credit' ? 'selected' : '' }}>دائن (له مال)</option>
        </select>
        @error('balance_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($customer)
             <input type="hidden" name="balance_type" value="{{ $customer->balance_type }}">
             <small class="form-text text-muted">نوع الرصيد الافتتاحي لا يُعدّل.</small>
        @endif
    </div>
    
    {{-- Notes --}}
    <div class="col-md-12 mb-3">
        <label for="notes" class="form-label">ملاحظات</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>