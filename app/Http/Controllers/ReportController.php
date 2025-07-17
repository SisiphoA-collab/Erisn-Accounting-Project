<?php
// app/Http/Controllers/AccountController.php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::query()->with('company');
        $company = Company::select('id', 'name')->get();

        // Filter by company_id if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by account type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->get();
        return response()->json([$accounts, 'companies' => $company]);
    }

    public function show($id)
    {
        $account = Company::findOrFail($id);
        return response()->json($account);
    }

    public function profitLoss(Request $request)
    {
        $companyId = $request->query('company_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Validate inputs
        if (!$companyId || !$startDate || !$endDate) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Income total
        $income = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('account', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->sum('amount');

        // Expense total
        $expense = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('account', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->sum('amount');

        return response()->json([
            'income' => $income,
            'expense' => $expense,
            'profit' => $income - $expense,
        ]);
    }


    public function balanceSheet(Request $request)
    {
        $companyId = $request->query('company_id');
        $asOfDate = $request->query('as_of_date');

        $assets = Account::where('company_id', $companyId)->where('type', 'asset')->get();
        $liabilities = Account::where('company_id', $companyId)->where('type', 'liability')->get();
        $equity = Account::where('company_id', $companyId)->where('type', 'equity')->get();

        return response()->json([
            'as_of_date' => $asOfDate,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity
        ]);
    }
    public function balanceSheetPdf(Request $request)
    {
        $companyId = $request->query('company_id');
        $asOfDate = $request->query('as_of_date');

        $assets = Account::where('company_id', $companyId)->where('type', 'asset')->get();
        $liabilities = Account::where('company_id', $companyId)->where('type', 'liability')->get();
        $equity = Account::where('company_id', $companyId)->where('type', 'equity')->get();

        $html = view('reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'asOfDate'))->render();

        return response()->streamDownload(function () use ($html) {
            echo Browsershot::html($html)->pdf();
        }, 'balance_sheet_report.pdf');
    }

    public function profitLossPdf(Request $request)
    {
        $companyId = $request->query('company_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $income = Transaction::where('type', 'income')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $expenses = Transaction::where('type', 'expense')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $netProfit = $income->sum('amount') - $expenses->sum('amount');

        $html = view('reports.profit_loss', compact('income', 'expenses', 'netProfit', 'startDate', 'endDate'))->render();

        return response()->streamDownload(function () use ($html) {
            echo Browsershot::html($html)->pdf();
        }, 'profit_loss_report.pdf');
    }
}
