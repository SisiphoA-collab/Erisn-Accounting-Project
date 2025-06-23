<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Stipend;
use Carbon\Carbon;

class DashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sentInvoice = Invoice::with(['customer:id,name'])
        ->select(['id', 'status', 'customer_id','updated_at'])
        ->where('status', 'Sent')
        ->orderBy('updated_at', 'desc')->first();

        $paidLearner = Stipend::with(['learner:id,name'])
        ->select(['id', 'status', 'learner_id','updated_at'])
        ->where('status', 'Paid')
        ->orderBy('updated_at', 'desc')->first();

        $currentYear = now()->year;

        // Initialize month labels
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Initialize data buckets for each status
        $statusMap = [
            'Paid' => ['label' => 'Paid', 'data' => [], 'backgroundColor' => '#4CAF50'],
            'Draft' => ['label' => 'Draft', 'data' => [], 'backgroundColor' => '#2196F3'],
            'Overdue' => ['label' => 'Overdue', 'data' => [], 'backgroundColor' => '#F44336'],
            'Sent' => ['label' => 'Sent', 'data' => [], 'backgroundColor' => '#FFC107'],
        ];

        // Initialize all values to 0 for each month
        foreach ($statusMap as $statusData) {
            foreach ($monthLabels as $month) {
                $statusData['data'][$month] = 0;
            }
        }

        // Fetch current year's invoices grouped by month + status
        $invoices = Invoice::selectRaw("MONTH(created_at) as month, status, SUM(amount) as total")
            ->whereYear('created_at', $currentYear)
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        // Populate the datasets
        foreach ($invoices as $invoice) {
            $month = $monthLabels[$invoice->month - 1];
            if (isset($statusMap[$invoice->status])) {
                $statusMap[$invoice->status]['data'][$month] = $invoice->total;
            }
        }

        $totalInvoices = $this->totalInvoices();
        // $data = $this->chart();
        $paidInvoices = 'R' . $this->invoicePaid();
        $overdueInvoices = 'R' . $this->invoiceOverdue();

        return response()->json([
            'invoice' => $sentInvoice,
            'totalInvoices' => $totalInvoices,
            'overdueInvoices' => $overdueInvoices,
            'paidInvoices' => $paidInvoices,
            'stipend' => $paidLearner,
            'labels' => $monthLabels,
            'datasets' => array_values($statusMap),
        ]);
    }

    public function totalInvoices()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalInvoices = Invoice::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        return $totalInvoices;
    }
    public function chart()
    {
        $currentYear = now()->year;

        // Initialize month labels
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Initialize data buckets for each status
        $statusMap = [
            'Paid' => ['label' => 'Paid', 'data' => [], 'backgroundColor' => '#4CAF50'],
            'Draft' => ['label' => 'Draft', 'data' => [], 'backgroundColor' => '#2196F3'],
            'Overdue' => ['label' => 'Overdue', 'data' => [], 'backgroundColor' => '#F44336'],
            'Sent' => ['label' => 'Sent', 'data' => [], 'backgroundColor' => '#FFC107'],
        ];

        // Initialize all values to 0 for each month
        foreach ($statusMap as &$statusData) {
            foreach ($monthLabels as $month) {
                $statusData['data'][$month] = 0;
            }
        }

        // Fetch current year's invoices grouped by month + status
        $invoices = Invoice::selectRaw("MONTH(created_at) as month, status, SUM(amount) as total")
            ->whereYear('created_at', $currentYear)
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        // Populate the datasets
        foreach ($invoices as $invoice) {
            $month = $monthLabels[$invoice->month - 1];
            if (isset($statusMap[$invoice->status])) {
                $statusMap[$invoice->status]['data'][$month] = $invoice->total;
            }
        }

        // Convert data into array of datasets and return
        return response()->json([
            'labels' => $monthLabels,
            'datasets' => array_values($statusMap),
        ]);
    }

    // public function chart()
    // {
    //     $invoices = Invoice::selectRaw("MONTH(created_at) as month, status, SUM(amount) as total")
    //         ->groupBy('month', 'status')
    //         ->orderBy('month', 'asc')
    //         ->get();

    //     $formattedData = [
    //         // 'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    //         'datasets' => [
    //             ['label' => 'Paid', 'data' => [], 'backgroundColor' => '#4CAF50'],
    //             ['label' => 'Draft', 'data' => [], 'backgroundColor' => '#2196F3'],
    //             ['label' => 'Overdue', 'data' => [], 'backgroundColor' => '#F44336'],
    //             ['label' => 'Sent', 'data' => [], 'backgroundColor' => '#FFC107'],
    //         ],
    //     ];

    //     foreach ($invoices as $invoice) {
    //         $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    //         $index = $monthLabels[$invoice->month - 1];
    //         switch ($invoice->status) {
    //             case 'Paid':
    //                 $formattedData['datasets'][0]['data'][$index] = $invoice->total;
    //                 break;
    //             case 'Draft':
    //                 $formattedData['datasets'][1]['data'][$index] = $invoice->total;
    //                 break;
    //             case 'Overdue':
    //                 $formattedData['datasets'][2]['data'][$index] = $invoice->total;
    //                 break;
    //             case 'Sent':
    //                 $formattedData['datasets'][3]['data'][$index] = $invoice->total;
    //                 break;
    //         }
    //     }

    //     return $formattedData;
    // }
    public function invoicePaid()
    {
        return Invoice::where('status', 'Paid')->sum('amount');
    }
    public function invoiceOverdue()
    {
        return Invoice::where('status', 'Overdue')->orWhere('status', 'Sent')->sum('amount');
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $invoice = Invoice::create($request->all());

    //     //increase customer balance
    //     $customer = Customer::Find($invoice->customer_id);
    //     $customer->balance += $invoice->amount;
    //     $customer->save();

    //     return response()->json([$invoice, 'message' => 'Invoices created successfully.']);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $invoice = Invoice::with('customer.company')->findOrFail($id);
    //     return response()->json($invoice);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $invoice = Invoice::findOrFail($id);
    //     $oldAmount = (float) $invoice->amount;
    //     $newAmount = (float) $request->amount;
    //     $balanceDelta = $newAmount - $oldAmount;

    //     //increase or decrease customer balance
    //     $customer = Customer::Find($invoice->customer_id);
    //     $customer->balance += $balanceDelta;
    //     $customer->save();
    //     $invoice->update($request->all());
    //     return response()->json([$invoice, 'message' => 'Invoice updated successfully.', 'type' => 'success']);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $invoice = Invoice::findOrFail($id);
    //    //decrease customer balance
    //     $customer = Customer::Find($invoice->customer_id);
    //     $customer->balance -= $invoice->amount;
    //     $customer->save();
    //     $invoice->delete();
    //     return response()->json(['message' => 'Invoice deleted', 'type' => 'message']);
    // }
}
