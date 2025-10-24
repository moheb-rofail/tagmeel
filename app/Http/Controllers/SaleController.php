<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;       // نحتاج لنموذج الصنف لتحديث المخزون
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sales = Sale::latest()->paginate(15);
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $items = Item::all(); // لتعبئة قائمة الأصناف في النموذج
        return view('sales.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. إنشاء فاتورة البيع الرئيسية
            $sale = Sale::create([
                'customer_name' => $validated['customer_name'] ?? null,
                'sale_date' => $validated['sale_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'total_amount' => $validated['total_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'final_amount' => $validated['final_amount'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. إضافة الأصناف وتحديث المخزون
            foreach ($validated['items'] as $itemData) {
                // حفظ تفاصيل الصنف في SaleItem
                $sale->items()->create($itemData);

                // تحديث كمية المخزون (خصم الكمية)
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $item->stock_quantity -= $itemData['quantity'];
                    $item->save();

                    // ** إضافة سجل حركة المخزون **
                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'movement_date' => $validated['sale_date'],
                        'movement_type' => 'OUT',
                        'quantity_change' => $itemData['quantity'],
                        'reference_type' => 'Sale',
                        'reference_id' => $sale->id,
                        'reason' => 'Sale Transaction',
                        'current_stock' => $item->stock_quantity,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sales.index')
                ->with('success', 'تم تسجيل عملية البيع بنجاح وتحديث المخزون!');

        } catch (\Exception $e) {
            DB::rollBack();
            // يجب تسجيل الخطأ هنا
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تسجيل عملية البيع. يرجى مراجعة التفاصيل.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): View
    {
        $sale->load('items.item');
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale): View
    {
        $items = Item::all();
        $sale->load('items.item');
        return view('sales.edit', compact('sale', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        // منطق التحديث معقد لأنه يتطلب عكس تأثيرات المخزون القديمة وتطبيق تأثيرات جديدة.
        // لعرض الهيكل، سنقوم بتحديث الرأس فقط. يجب إضافة منطق معقد للأصناف والمخزون.

        DB::beginTransaction();
        try {
            // تحديث فاتورة البيع الرئيسية
            $sale->update($request->only([
                'customer_name',
                'sale_date',
                'invoice_number',
                'total_amount',
                'discount_amount',
                'final_amount',
                'payment_method',
                'notes'
            ]));

            // NOTE: يجب هنا إضافة منطق تحديث أصناف SaleItem وتعديل المخزون بشكل آمن.

            DB::commit();
            return redirect()->route('sales.index')
                ->with('success', 'تم تحديث عملية البيع بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تحديث عملية البيع.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        // يتطلب منطق عكس المخزون (إضافة الكميات المحذوفة) قبل الحذف الكامل.

        // مثال على منطق عكس المخزون (يجب أن يتم داخل معاملة DB::transaction):
        /*
        foreach ($sale->items as $saleItem) {
            $item = Item::find($saleItem->item_id);
            if ($item) {
                $item->stock_quantity += $saleItem->quantity;
                $item->save();
            }
        }
        */

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'تم حذف عملية البيع (و يفترض عكس المخزون المرتبط بها) بنجاح!');
    }
}