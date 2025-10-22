<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $expenses = Expense::latest()->paginate(15);
        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle receipt upload (if file is present)
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('public/receipts');
            // Store the path relative to the 'storage' disk
            $data['receipt_path'] = Storage::url($path); 
        }

        Expense::create($data);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense): View
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): View
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle receipt upload (if a new file is present)
        if ($request->hasFile('receipt')) {
            // Optional: Delete old receipt if it exists
            if ($expense->receipt_path) {
                // Get the actual storage path from the URL
                $oldPath = str_replace('/storage', 'public', $expense->receipt_path);
                Storage::delete($oldPath);
            }

            $path = $request->file('receipt')->store('public/receipts');
            $data['receipt_path'] = Storage::url($path);
        }

        $expense->update($data);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        // Optional: Delete associated receipt file when the expense is deleted
        if ($expense->receipt_path) {
            $path = str_replace('/storage', 'public', $expense->receipt_path);
            Storage::delete($path);
        }
        
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }
}