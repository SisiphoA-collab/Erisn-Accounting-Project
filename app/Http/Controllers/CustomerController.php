<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('user_id', auth()->id())->get();
        return response()->json(['customers' => $customers]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'balance' => 'required|numeric',
        ]);

        $customer = Customer::create([
            'user_id' => auth()->id(),
            'company_id' => $request->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'balance' => $request->balance,
        ]);

        return response()->json($customer, 201);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::where('user_id', auth()->id())->findOrFail($id);
        $customer->update($request->only(['company_id', 'name', 'email', 'balance']));
        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::where('user_id', auth()->id())->findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}