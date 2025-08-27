<?php

namespace App\Http\Controllers;

use App\Models\BankAccount; // Use BankAccount instead of Account
use App\Models\Transaction;
use App\Models\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankingController extends Controller
{
    public function index(Request $request)
    {
        $accountQuery = BankAccount::select('id', 'account_number', 'account_holder', 'account_type', 'status'); // Adjusted fields
        $transactionQuery = Transaction::with('bankAccount:id,account_holder')->select('id', 'bank_account_id', 'amount', 'status', 'date'); // Adjusted relationship
        $reconciliationQuery = Reconciliation::with('bankAccount:id,account_holder')->select('id', 'bank_account_id', 'date', 'status', 'notes'); // Adjusted relationship

        if ($request->filled('status') && $request->status !== 'All') {
            $accountQuery->where('status', $request->status);
            $transactionQuery->where('status', $request->status);
            $reconciliationQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $accountQuery->where(function ($q) use ($searchTerm) {
                $q->where('account_number', 'like', $searchTerm)
                  ->orWhere('account_holder', 'like', $searchTerm); // Adjusted to account_holder
            });
            $transactionQuery->whereHas('bankAccount', function ($q) use ($searchTerm) {
                $q->where('account_holder', 'like', $searchTerm); // Adjusted to account_holder
            });
            $reconciliationQuery->whereHas('bankAccount', function ($q) use ($searchTerm) {
                $q->where('account_holder', 'like', $searchTerm); // Adjusted to account_holder
            });
        }

        $accounts = $accountQuery->orderBy('id', 'desc')->paginate(10);
        $transactions = $transactionQuery->orderBy('date', 'desc')->paginate(10);
        $reconciliations = $reconciliationQuery->orderBy('date', 'desc')->paginate(10);

        $activeAccountsCount = BankAccount::where('status', 'Active')->count();
        $pendingReconciliationsCount = Reconciliation::where('status', 'Pending')->count();

        return response()->json([
            'accounts' => $accounts,
            'transactions' => $transactions,
            'reconciliations' => $reconciliations,
            'stats' => [
                'active_accounts' => $activeAccountsCount,
                'pending_reconciliations' => $pendingReconciliationsCount,
            ],
        ]);
    }

    public function storeAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|unique:bank_accounts,account_number', // Updated table
            'account_holder' => 'required|string|max:255', // Changed from account_name
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $account = BankAccount::create([ // Use BankAccount
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder, // Changed from account_name
            'balance' => $request->balance,
            'status' => 'Active',
        ]);

        return response()->json(['message' => 'Account added successfully.', 'account' => $account], 201);
    }

    public function updateAccount(Request $request, $id)
    {
        $account = BankAccount::find($id); // Use BankAccount

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $id, // Updated table
            'account_holder' => 'required|string|max:255', // Changed from account_name
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $account->update([
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder, // Changed from account_name
            'balance' => $request->balance,
        ]);

        return response()->json(['message' => 'Account updated successfully.', 'account' => $account]);
    }

    public function destroyAccount($id)
    {
        $account = BankAccount::find($id); // Use BankAccount

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $account->delete();

        return response()->json(['message' => 'Account deleted successfully.']);
    }

    public function storeTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id', // Changed from account_id
            'amount' => 'required|numeric',
            'status' => 'required|in:Pending,Processed',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $transaction = Transaction::create([
            'bank_account_id' => $request->bank_account_id, // Changed from account_id
            'amount' => $request->amount,
            'status' => $request->status,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Transaction logged successfully.', 'transaction' => $transaction], 201);
    }

    public function updateTransaction(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id', // Changed from account_id
            'amount' => 'required|numeric',
            'status' => 'required|in:Pending,Processed',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $transaction->update([
            'bank_account_id' => $request->bank_account_id, // Changed from account_id
            'amount' => $request->amount,
            'status' => $request->status,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Transaction updated successfully.', 'transaction' => $transaction]);
    }

    public function destroyTransaction($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully.']);
    }

    public function storeReconciliation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id', // Changed from account_id
            'date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $reconciliation = Reconciliation::create([
            'bank_account_id' => $request->bank_account_id, // Changed from account_id
            'date' => $request->date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Reconciliation saved successfully.', 'reconciliation' => $reconciliation], 201);
    }

    public function updateReconciliation(Request $request, $id)
    {
        $reconciliation = Reconciliation::find($id);

        if (!$reconciliation) {
            return response()->json(['message' => 'Reconciliation not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id', // Changed from account_id
            'date' => 'required|date',
            'status' => 'required|in:Pending,Completed,Failed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $reconciliation->update([
            'bank_account_id' => $request->bank_account_id, // Changed from account_id
            'date' => $request->date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Reconciliation updated successfully.', 'reconciliation' => $reconciliation]);
    }

    public function destroyReconciliation($id)
    {
        $reconciliation = Reconciliation::find($id);

        if (!$reconciliation) {
            return response()->json(['message' => 'Reconciliation not found.'], 404);
        }

        $reconciliation->delete();

        return response()->json(['message' => 'Reconciliation deleted successfully.']);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $file = $request->file('csv_file');
        $filePath = $file->store('imports');

        $csvData = array_map('str_getcsv', file(Storage::path($filePath)));
        foreach (array_slice($csvData, 1) as $row) {
            BankAccount::updateOrCreate( // Use BankAccount
                ['account_number' => $row[0]],
                ['account_holder' => $row[1] ?? 'Imported Account', 'balance' => $row[2] ?? 0, 'status' => 'Active']
            );
        }

        Storage::delete($filePath);

        return response()->json(['message' => 'Statements imported successfully.']);
    }
}