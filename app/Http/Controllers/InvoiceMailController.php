<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

use function PHPSTORM_META\type;

class InvoiceMailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|integer|exists:invoices,id',
            'html' => 'required|string',
            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    if (!str_contains($value, 'paystack.com')) {
                        $fail('The URL must be a Paystack link.');
                    }
                },
            ],
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

        try {
            $invoice = Invoice::with('customer')->findOrFail($request->invoice_id);

            if (!$invoice->customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            $fileName = $invoice->customer->name . 'invoice_' . $invoice->id . '.pdf';
            $fullPath = storage_path('app/invoices/' . $fileName); // Fix missing slash

            // Ensure directory exists
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // Generate PDF
            Browsershot::html($html)
                ->setOption('args', ['--disable-web-security', '--no-sandbox', '--headless=new'])
                ->showBackground()
                ->format('A4')
                ->savePdf($fullPath);

            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'PDF generation failed'], 500);
            }

            // Get payment URL
            $url = $request->url;

            // Send email
            Mail::to($invoice->customer->email)
                ->send(new InvoiceMail($invoice, $fullPath, $url));

            // Clean up file
            File::delete($fullPath);

            return response()->json([
                'message' => 'Invoice sent successfully.',
                'type' => 'success',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['messge' => 'Invoice not found','type'=>'error'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'messge' => 'An error occurred while processing the invoice.',
                'type' => 'error'
            ], 500);
        }
    }
}
