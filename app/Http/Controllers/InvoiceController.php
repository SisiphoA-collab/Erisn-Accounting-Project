<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

use function PHPSTORM_META\type;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['customer:id,name,company_id'],
            ['customer.company:id,name,industry'])
            ->select('id', 'amount', 'status', 'due_date', 'customer_id', 'updated_at');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $invoice = $query->orderBy('updated_at', 'desc')->paginate(10);
        $customer = Customer::get(['name', 'id']);
        return response()->json(['invoices' => $invoice, 'customers' => $customer]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice = Invoice::create($request->all());

        //increase customer balance
        $customer = Customer::Find($invoice->customer_id);
        $customer->balance += $invoice->amount;
        $customer->save();

        return response()->json([$invoice, 'message' => 'Invoices created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::with('customer.company')->findOrFail($id);
        return response()->json($invoice);
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
        $invoice = Invoice::findOrFail($id);
        $oldAmount = (float) $invoice->amount;
        $newAmount = (float) $request->amount;
        $balanceDelta = $newAmount - $oldAmount;

        //increase or decrease customer balance
        $customer = Customer::Find($invoice->customer_id);
        $customer->balance += $balanceDelta;
        $customer->save();
        $invoice->update($request->all());
        return response()->json([$invoice, 'message' => 'Invoice updated successfully.', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
       //decrease customer balance
        $customer = Customer::Find($invoice->customer_id);
        $customer->balance -= $invoice->amount;
        $customer->save();
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted', 'type' => 'message']);
    }

    public function getInvoiceStats()
    {
        $stats = Invoice::selectRaw('status, COUNT(*) as invoices_stats')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return response()->json(['data' => $stats]);
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'html' => 'required|string',
            'customer_name' => 'required|string',
            'invoice_id' => 'required|integer|exists:invoices,id',
        ]);
        $rawHtml = $request->input('html');

        // Wrap with Bootstrap
        $html = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Invoice</title>

                    <!-- Bootstrap CDN (optional) -->
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link href="' . public_path('build/assets/app.css') . '" rel="stylesheet">

                    <style>
                        body { padding: 2rem; font-size: 14px; }
                    </style>
                </head>
                <body>' . $rawHtml . '</body>
                </html>
            ';

        $fileName = $request->customer_name . '_invoice_' . $request->invoice_id .  '.pdf';
        $pdfPath = storage_path("app/public/invoices/{$fileName}");

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        try {
            Browsershot::html($html)
                ->setOption('args', ['--disable-web-security', '--no-sandbox', '--headless=new'])
                ->showBackground()
                ->format('A4')
                ->save($pdfPath);

            Storage::disk('public')->put("invoices/{$fileName}", file_get_contents($pdfPath));

            return response()->json([
                'url' => asset("storage/invoices/{$fileName}"),
                'message' => 'Invoice downloaded successfully.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate PDF: ' . $e->getMessage(), 'type' => 'error'], 500);
        }
    }
}
