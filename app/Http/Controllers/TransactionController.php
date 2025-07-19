<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account','creator']);

        // Filter by account_id if provided
        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->orderBy('date', 'desc')->get();
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:debit,credit',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Create the transaction
            $transaction = Transaction::create([
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'type' => $request->type,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            // Update the account balance
            $account = Account::findOrFail($request->account_id);

            if ($request->type === 'debit') {
                // For assets and expenses, debits increase the balance
                if (in_array($account->type, ['asset', 'expense'])) {
                    $account->balance += $request->amount;
                } else {
                    // For liabilities, equity, and income, debits decrease the balance
                    $account->balance -= $request->amount;
                }
            } else { // credit
                // For assets and expenses, credits decrease the balance
                if (in_array($account->type, ['asset', 'expense'])) {
                    $account->balance -= $request->amount;
                } else {
                    // For liabilities, equity, and income, credits increase the balance
                    $account->balance += $request->amount;
                }
            }

            $account->save();

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create transaction', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['account', 'creator'])->findOrFail($id);
        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        // In a real accounting system, you typically wouldn't allow editing transactions after they're created
        // Instead, you'd create reversing entries and new transactions
        return response()->json(['message' => 'Editing transactions is not allowed. Create reversing entry instead.'], 422);
    }

    public function destroy($id)
    {
        // In a real accounting system, you typically wouldn't allow deleting transactions after they're created
        // Instead, you'd create reversing entries
        return response()->json(['message' => 'Deleting transactions is not allowed. Create reversing entry instead.'], 422);
    }
}
