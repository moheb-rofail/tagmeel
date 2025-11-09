<x-layout>
<div class="container text-right" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4>تسجيل سداد مديونية عميل</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer_payments.store') }}" method="POST">
                        @csrf
                        
                        {{-- قائمة العملاء --}}
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">اختر العميل <span class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">-- اختر عميل --</option>
                                @foreach ($customers as $c)
                                    @php
                                        // تحديد الخيار المختار مسبقاً
                                        $isSelected = (old('customer_id') == $c->id) || ($customer && $customer->id == $c->id);
                                        $balanceText = ($c->balance_type == 'Debit' ? 'مدين (عليه)' : 'دائن (له)');
                                        $balanceDisplay = number_format($c->current_balance, 2);
                                        $balanceInfo = " (الرصيد: {$balanceDisplay} ر.س - {$balanceText})";
                                    @endphp
                                    <option value="{{ $c->id }}" data-balance-type="{{ $c->balance_type }}" data-balance="{{ $c->current_balance }}" 
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $c->name . $balanceInfo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            {{-- لعرض رصيد العميل بشكل حي --}}
                            <div class="mt-2 alert alert-warning d-none" id="customer-balance-info"></div>
                        </div>

                        {{-- تاريخ السداد --}}
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">تاريخ السداد <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date"
                                   value="{{ old('payment_date', now()->toDateString()) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- مبلغ السداد --}}
                        <div class="mb-3">
                            <label for="amount" class="form-label">المبلغ المسدّد <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control text-left @error('amount') is-invalid @enderror" id="amount" name="amount"
                                   value="{{ old('amount') }}" placeholder="أدخل مبلغ السداد" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- طريقة الدفع --}}
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">طريقة الدفع</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="نقد" selected>نقد</option>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                                <option value="شيك">شيك</option>
                                <option value="آخرى">آخرى</option>
                            </select>
                        </div>
                        
                        {{-- ملاحظات --}}
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="mt-4 text-left">
                            <button type="submit" class="btn btn-success ms-2">تسجيل السداد</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- سكريبت لعرض رصيد العميل بشكل حي --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerSelect = document.getElementById('customer_id');
        const balanceInfoDiv = document.getElementById('customer-balance-info');

        function updateBalanceInfo() {
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const balanceType = selectedOption.getAttribute('data-balance-type');
            const balance = selectedOption.getAttribute('data-balance');
            
            if (balance && selectedOption.value) {
                const balanceDisplay = parseFloat(balance).toFixed(2);
                let message = `الرصيد الحالي: **${balanceDisplay}** ر.س.`;
                
                if (balanceType === 'Debit' && parseFloat(balance) > 0) {
                    message += `<br>العميل **مدين لك** بهذا المبلغ (عليه دين).`;
                    balanceInfoDiv.className = 'mt-2 alert alert-danger';
                } else if (balanceType === 'Credit' && parseFloat(balance) > 0) {
                    message += `<br>العميل **دائن لك** بهذا المبلغ (له مال زائد).`;
                    balanceInfoDiv.className = 'mt-2 alert alert-success';
                } else {
                    message = `الرصيد الحالي صفر.`;
                    balanceInfoDiv.className = 'mt-2 alert alert-secondary';
                }
                
                balanceInfoDiv.innerHTML = message;
                balanceInfoDiv.classList.remove('d-none');
            } else {
                balanceInfoDiv.classList.add('d-none');
            }
        }

        customerSelect.addEventListener('change', updateBalanceInfo);
        
        // تشغيل الدالة عند التحميل في حال كان هناك عميل محدد مسبقاً
        if (customerSelect.value) {
            updateBalanceInfo();
        }
    });
</script>
</x-layout>