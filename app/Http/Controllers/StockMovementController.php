<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Item;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $movements = StockMovement::with('item')
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('stock_movements.index', compact('movements'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // تحميل العلاقة 'item' للتأكد من أنها متاحة في الـ View
        $movement = StockMovement::find($id);
        //dd($movement);
        return view('stock_movements.show', compact('movement'));
    }

    public function itemHistory(Item $item): \Illuminate\View\View
    {
        // فلترة حركات المخزون حسب مُعرّف الصنف، مع ترتيب تنازلي حسب التاريخ وID
        $movements = StockMovement::where('item_id', $item->id)
            ->with('item') // تحميل بيانات الصنف
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('stock_movements.item_history', compact('movements', 'item'));
    }
    // دوال create, store, edit, update, destroy غير موجودة هنا.
}