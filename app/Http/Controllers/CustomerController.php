<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $customers = Customer::orderBy('name')->paginate(15);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // عند الإنشاء، يتم تعيين الرصيد الحالي ليكون مساوياً للرصيد الافتتاحي
        $validated['current_balance'] = $validated['initial_balance'] ?? 0;

        Customer::create($validated);
        
        return redirect()->route('customers.index')
            ->with('success', 'تم تسجيل البائع/الزبون بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        // يمكن هنا تحميل سجلات المبيعات الآجلة للعميل
        // تحميل علاقة المدفوعات (CustomerPayments) مع ترتيبها تنازليًا حسب التاريخ
        $customer->load(['payments' => function ($query) {
            $query->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }]);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();
        
        // عند التحديث، يجب الحذر: إذا تم تغيير الرصيد الافتتاحي، يجب تعديل الرصيد الحالي أيضاً
        // ولكن يفضل عدم السماح بتعديل initial_balance بعد أول إنشاء لتجنب تعقيدات محاسبية.
        
        $customer->update($validated);
        
        return redirect()->route('customers.index')
            ->with('success', 'تم تحديث بيانات البائع/الزبون بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        // تحقق من عدم وجود مبيعات مرتبطة به أو رصيد غير صفري قبل الحذف
        if ($customer->current_balance != 0 || $customer->sales()->exists()) {
             return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الزبون لوجود رصيد أو معاملات سابقة.');
        }

        $customer->delete();
        
        return redirect()->route('customers.index')
            ->with('success', 'تم حذف البائع/الزبون بنجاح.');
    }
}