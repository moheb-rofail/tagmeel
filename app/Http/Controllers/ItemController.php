<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource (The Stock List).
     */
    public function index()
    {
        // Fetch items, ordering by stock level to easily see low stock
        $items = Item::orderBy('stock_quantity')->paginate(15);

        return view('items.index', compact('items'));
    }

    /**
     * Store a newly created item in storage.
     * Initial stock can be set here.
     */
    public function store(ItemStoreRequest $request)
    {
        Item::create($request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully. Stock is now managed.');
    }

    /**
     * Update the specified item in storage.
     * This primarily handles metadata and pricing updates (e.g., changing the selling price).
     * Stock updates are handled by PurchaseController and SaleController.
     */
    public function update(ItemUpdateRequest $request, Item $item)
    {
        // Fill and save the updated data
        $item->update($request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Item details and pricing updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        // In a real system, you'd check if any Sales/Purchases reference this item
        // before deleting. For simplicity, we just delete here.
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }
}
