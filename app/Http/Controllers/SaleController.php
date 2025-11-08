<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Item;       // نحتاج لنموذج الصنف لتحديث المخزون
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

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
        $items = Item::all();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('items', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request): RedirectResponse
    {
        // dd($request->all());
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // determine customer id/name
            $customerId = $validated['customer_id'] ?? null;
            if ($customerId) {
                $customer = Customer::find($customerId);
                $customerName = $customer ? $customer->name : ($validated['customer_name'] ?? 'عميل نقدي');
            } else {
                $customerName = $validated['customer_name'] ?? 'عميل نقدي';
            }

            // إنشاء فاتورة البيع الرئيسية
            $sale = Sale::create([
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'sale_date' => $validated['sale_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'total_amount' => $validated['total_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'final_amount' => $validated['final_amount'],
                'payment_status' => $validated['payment_status'] ?? 'Not Paid',
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->has('add_to_balance') && $request->filled('customer_id')) {
                $customer = Customer::findOrFail($request->customer_id);
                $unpaidAmount = $request->final_amount - $request->paid_amount;
                
                if ($unpaidAmount > 0) {
                    $customer->current_balance += $unpaidAmount;
                    $customer->balance_type = 'Debit';
                    $customer->save();
                }
            }

            // إضافة الأصناف وتحديث المخزون
            foreach ($validated['items'] as $itemData) {
                // map fields explicitly to avoid wrong keys / mass assignment issues
                $saleItemData = [
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'sub_total' => $itemData['sub_total'],
                ];

                $sale->items()->create($saleItemData);

                // تحديث كمية المخزون (خصم الكمية)
                $item = Item::find($saleItemData['item_id']);
                if ($item) {
                    $item->stock_quantity -= $saleItemData['quantity'];
                    $item->save();

                    // سجل حركة المخزون
                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'movement_date' => $validated['sale_date'],
                        'movement_type' => 'OUT',
                        'quantity_change' => $saleItemData['quantity'],
                        'reference_type' => 'Sale',
                        'reference_id' => $sale->id,
                        'reason' => 'Sale Transaction',
                        'current_stock' => $item->stock_quantity,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales.print.thermal', $sale)->with('success', 'تم تسجيل عملية البيع بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale store failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تسجيل عملية البيع. راجع السجل (storage/logs/laravel.log).');
        }
    }

    /**
     * يعرض عملية البيع ويجهزها للطباعة الحرارية.
     */
    public function printThermalSale($id) // لاحظ تغيير الاسم إلى Sale
    {   
        // يفضل استخدام Eager Loading لتقليل الاستعلامات
        $sale = Sale::with('items.item')->findOrFail($id);

        // Debug each relationship level
    // dd([
    //     'sale' => $sale->toArray(),
    //     'items' => $sale->items->toArray(),
    //     'first_item_details' => $sale->items->first() ? [
    //         'item_data' => $sale->items->first()->item->toArray(),
    //         'sale_item_data' => $sale->items->first()->toArray()
    //     ] : null
    // ]);
        // عرض الـ Blade المصمم للطباعة الحرارية
        return view('sales.thermal_print', compact('sale')); // لاحظ تغيير المجلد إلى sales
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
    $customers = Customer::orderBy('name')->get();
    $sale->load('items.item');
    return view('sales.edit', compact('sale', 'items', 'customers'));
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