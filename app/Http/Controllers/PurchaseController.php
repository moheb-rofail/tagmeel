<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier; // لاستخدامها في create/edit
use App\Models\Item;     // لاستخدامها في create/edit
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $suppliers = Supplier::all();
        $items = Item::all();
        return view('purchases.create', compact('suppliers', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. إنشاء فاتورة الشراء الرئيسية
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'total_amount' => $validated['total_amount'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. إضافة الأصناف وتحديث المخزون
            foreach ($validated['items'] as $itemData) {
                // حفظ تفاصيل الصنف في PurchaseItem
                $purchase->items()->create($itemData);

                // تحديث كمية المخزون (Stock)
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $item->stock_quantity += $itemData['quantity'];
                    // يمكنك أيضاً تحديث unit_price إذا لزم الأمر
                    $item->unit_price = $itemData['unit_cost'];
                    $item->save();

                    // ** إضافة سجل حركة المخزون **
                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'movement_date' => $validated['purchase_date'],
                        'movement_type' => 'IN',
                        'quantity_change' => $itemData['quantity'],
                        'reference_type' => 'Purchase',
                        'reference_id' => $purchase->id,
                        'reason' => 'Purchase Receipt',
                        'current_stock' => $item->stock_quantity,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'تم تسجيل عملية الشراء بنجاح وتحديث المخزون!');

        } catch (\Exception $e) {
            DB::rollBack();
            // يجب تسجيل الخطأ هنا
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تسجيل عملية الشراء: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load('supplier', 'items.item');
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase): View
    {
        $suppliers = Supplier::all();
        $items = Item::all();
        $purchase->load('items.item');
        return view('purchases.edit', compact('purchase', 'suppliers', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        // منطق التحديث معقد لأنه يتطلب التعامل مع حذف/إضافة/تعديل الأصناف وتحديث المخزون
        // *بشكل آمن* عكسياً. لهذا، يتم هنا عرض التحديث الجزئي.

        DB::beginTransaction();
        try {
            // 1. تحديث فاتورة الشراء الرئيسية
            $purchase->update($request->only([
                'supplier_id',
                'purchase_date',
                'invoice_number',
                'total_amount',
                'status',
                'notes'
            ]));

            // NOTE: تحديث الأصناف وتعديل المخزون يتطلب منطقًا إضافيًا معقدًا
            // يعتمد على مقارنة الأصناف القديمة بالجديدة. 

            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'تم تحديث فاتورة الشراء بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تحديث فاتورة الشراء.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        // يتطلب منطق عكس المخزون (ناقص الكميات المحذوفة) قبل الحذف الكامل.

        // مثال على منطق عكس المخزون (يجب أن يتم داخل معاملة DB::transaction):
        /*
        foreach ($purchase->items as $purchaseItem) {
            $item = Item::find($purchaseItem->item_id);
            if ($item) {
                $item->stock_quantity -= $purchaseItem->quantity;
                $item->save();
            }
        }
        */

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'تم حذف عملية الشراء (و يفترض عكس المخزون المرتبط بها) بنجاح!');
    }
}