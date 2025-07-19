<?php
// app/Http/Controllers/AccountController.php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('company')->paginate(10);
        $company = Company::select('id', 'name')->get();
        return response()->json([$accounts, 'company' => $company]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'category' => 'nullable|in:current,non-current',
            'balance' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account = Account::create($request->all());
        return response()->json($account, 201);
    }

    public function show($id)
    {
        $account = Account::with('company')->findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'sometimes|required|exists:companies,id',
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:asset,liability,equity,income,expense',
            'category' => 'nullable|in:current,non-current',
            'balance' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account->update($request->all());
        return response()->json(['message' => 'Account updated successfully']);
    }

    public function destroy(Account $account)
    {
        if ($account->transactions()->count() > 0) {
            return response()->json(['message' => 'Cannot delete account with associated transactions'], 422);
        }

        $account->delete();
        return response()->json(['message' => 'Account Deleted']);
    }
}
