<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // جلب قائمة الموردين مع أحدثهم أولاً
        $suppliers = Supplier::latest()->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(SupplierStoreRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'تم تسجيل المورد بنجاح.');
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'تم تحديث بيانات المورد بنجاح.');
    }

    /**
     * Remove the specified supplier from storage.
     * يجب التحقق من عدم وجود مشتريات مرتبطة قبل الحذف.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // التحقق من وجود أي سجلات مشتريات مرتبطة
            if ($supplier->purchases()->exists()) {
                return redirect()->route('suppliers.index')
                    ->with('error', 'لا يمكن حذف المورد لوجود عمليات شراء مرتبطة به.');
            }

            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح.');
        } catch (\Exception $e) {
            // تسجيل الخطأ للمراجعة
            \Log::error('Supplier deletion failed: ' . $e->getMessage());

            return redirect()->route('suppliers.index')
                ->with('error', 'حدث خطأ أثناء حذف المورد.');
        }
    }
}
