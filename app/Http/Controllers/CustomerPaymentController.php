<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Http\Requests\StoreCustomerPaymentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class CustomerPaymentController extends Controller
{
    /**
     * Show the form for creating a new customer payment.
     */
    public function create(Customer $customer = null): View
    {
        // إذا تم تمرير ID العميل عبر المسار، يكون محدداً مسبقاً
        $customers = Customer::orderBy('name')->get();
        return view('customer_payments.create', compact('customers', 'customer'));
    }

    /**
     * Store a newly created customer payment and update customer balance.
     */
    public function store(StoreCustomerPaymentRequest $request): RedirectResponse
    {
        //dd($request->all());
        $validated = $request->validated();
        
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($validated['customer_id']);
            $paymentAmount = $validated['amount'];
            $previousBalance = $customer->current_balance;
            
            // 1. حساب الرصيد الجديد: 
            // بما أن العميل "مدين" (Debit)، فرصيده قيمة موجبة (مثلاً 500). السداد يطرح من هذه القيمة.
            $newBalance = $previousBalance - $paymentAmount;
            
            $newBalanceType = 'Debit';

            // 2. تحديد نوع الرصيد الجديد:
            if ($newBalance < 0) {
                // إذا أصبح الرصيد سالباً، فهذا يعني أن العميل أصبح دائناً (له مال زائد)
                $newBalance = abs($newBalance);
                $newBalanceType = 'Credit';
            } elseif ($newBalance == 0) {
                $newBalanceType = 'Debit'; 
            } else {
                // إذا كان الرصيد ما زال موجباً (أكبر من صفر)، يظل العميل مديناً
                $newBalanceType = 'Debit';
            }
            
            // 3. تحديث رصيد العميل في جدول Customers
            $customer->current_balance = $newBalance;
            $customer->balance_type = $newBalanceType;
            $customer->save();
            
            // 4. تسجيل حركة السداد في جدول CustomerPayments
            CustomerPayment::create([
                'customer_id' => $customer->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
                'notes' => $validated['notes'],
            ]);

            DB::commit();
            return redirect()->route('customers.show', $customer)
                ->with('success', "تم سداد مبلغ **" . number_format($paymentAmount, 2) . " ر.س** بنجاح. الرصيد الجديد: " . number_format($newBalance, 2) . " ($newBalanceType)");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تسجيل السداد: يرجى التحقق من المدخلات.');
        }
    }
}