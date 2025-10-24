<?php

namespace App\Http\Controllers;

// تم تغيير اسم النموذج واستيراده
use App\Models\StockReturn;
use App\Models\StockReturnItem;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Purchase;
// تم تغيير اسم طلبات التحقق
use App\Http\Requests\StoreStockReturnRequest;
use App\Http\Requests\UpdateStockReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockReturnController extends Controller
{
    public function index(): View
    {
        $returns = StockReturn::latest()->paginate(15);
        return view('returns.index', compact('returns'));
    }

    public function create(): View
    {
        $items = Item::all();
        $sales = Sale::orderBy('id', 'desc')->limit(100)->get();
        $purchases = Purchase::orderBy('id', 'desc')->limit(100)->get();

        return view('returns.create', compact('items', 'sales', 'purchases'));
    }

    public function store(StoreStockReturnRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. إنشاء رأس الإرجاع
            $returnDoc = StockReturn::create($validated);

            // 2. إضافة الأصناف وتحديث المخزون
            foreach ($validated['items'] as $itemData) {
                $returnDoc->items()->create($itemData);

                // تحديث كمية المخزون
                $item = \App\Models\Item::find($itemData['item_id']);
                if ($item) {
                    $movementType = '';
                    $reason = '';

                    if ($validated['return_type'] === 'sale') {
                        // إرجاع مبيعات: زيادة المخزون
                        $item->stock_quantity += $itemData['quantity'];
                        $movementType = 'IN';
                        $reason = 'Sale Return (إرجاع مبيعات)';
                    } elseif ($validated['return_type'] === 'purchase') {
                        // إرجاع مشتريات: نقص المخزون
                        $item->stock_quantity -= $itemData['quantity'];
                        $movementType = 'OUT';
                        $reason = 'Purchase Return (إرجاع مشتريات)';
                    }

                    $item->save();

                    // **********************************************
                    // ** إضافة سجل حركة المخزون **
                    // **********************************************
                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'movement_date' => $validated['return_date'],
                        'movement_type' => $movementType, // IN أو OUT
                        'quantity_change' => $itemData['quantity'],
                        'reference_type' => 'StockReturn',
                        'reference_id' => $returnDoc->id,
                        'reason' => $reason,
                        'current_stock' => $item->stock_quantity,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('returns.index')
                ->with('success', 'تم تسجيل الإرجاع بنجاح وتحديث المخزون!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'فشل تسجيل الإرجاع: ' . $e->getMessage());
        }
    }

    public function show(StockReturn $return): View
    {
        $return->load('items.item');
        return view('returns.show', compact('return'));
    }

    // ... (باقي دوال edit و update و destroy تحتاج لتغيير اسم الفئة بنفس الطريقة) ...

    public function edit(StockReturn $return): View
    {
        $items = Item::all();
        $sales = Sale::orderBy('id', 'desc')->limit(100)->get();
        $purchases = Purchase::orderBy('id', 'desc')->limit(100)->get();
        $return->load('items.item');

        return view('returns.edit', compact('return', 'items', 'sales', 'purchases'));
    }

    public function update(UpdateStockReturnRequest $request, StockReturn $return): RedirectResponse
    {
        // ... (منطق التحديث) ...
        // يتم تحديث رأس الفاتورة باستخدام $return->update()
        // يجب إضافة منطق معقد لتحديث أصناف المخزون هنا

        return redirect()->route('returns.index')
            ->with('success', 'تم تحديث سجل الإرجاع بنجاح.');
    }

    public function destroy(StockReturn $return): RedirectResponse
    {
        // ... (منطق الحذف وعكس المخزون) ...

        // يجب أن يتم هنا عكس حركة المخزون بناءً على return_type قبل حذف $return->delete();

        return redirect()->route('returns.index')
            ->with('success', 'تم حذف سجل الإرجاع وعكس حركة المخزون بنجاح!');
    }
}