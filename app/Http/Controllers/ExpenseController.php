<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Expense::with('vendor');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vendor', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhere('amount', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(10);

        return response()->json($expenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'date' => 'required|date',
        ]);

        $expense = Expense::create($request->all());

        $vendor = Vendor::find($expense->vendor_id);
        $vendor->balance += $expense->amount;
        $vendor->save();

        $expense->load('vendor');

        return response()->json([
            'message' => 'Expense added successfully.',
            'expense' => $expense,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $expense = Expense::findOrFail($id);

        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'date' => 'required|date',
        ]);

        if ($expense->vendor_id !== $request->vendor_id || $expense->amount != $request->amount) {
            $oldVendor = Vendor::find($expense->vendor_id);
            $oldVendor->balance -= $expense->amount;
            $oldVendor->save();

            $newVendor = Vendor::find($request->vendor_id);
            $newVendor->balance += $request->amount;
            $newVendor->save();
        }

        $expense->update($request->all());

        return response()->json(['message' => 'Expense updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        $vendor = Vendor::find($expense->vendor_id);
        $vendor->balance -= $expense->amount;
        $vendor->save();

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully.']);
    }
}
