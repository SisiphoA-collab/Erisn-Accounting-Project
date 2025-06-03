<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment = Payment::all();

        return response()->json([
            'payments' => $payment,
        ]);
    }

    public function initializePaystack(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $customer = $invoice->customer;

        $data = [
            "email" => $customer->email,
            "amount" => $invoice->amount * 100, // Paystack uses kobo
            "callback_url" => url('/api/paystack/callback?invoice_id=' . $invoice->id),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer sk_test_44ded719e57730f861636fefbb857ae6fdee42f0'. env('PAYSTACK_SECRET_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', $data);

        return response()->json([
            'authorization_url' => $response['data']['authorization_url']
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $payment = Payment::create($request->all());

        // //Decreace Customer Balance 
        // $invoice = Invoice::find($payment->invoice_id);
        // $customer = Customer::find($invoice->customer_id);
        // $customer->balance -= $payment->amount;
        // $customer->save();

        // return $payment;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function paystackCallback(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $trxRef = $request->query('reference');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer sk_test_44ded719e57730f861636fefbb857ae6fdee42f0' . env('PAYSTACK_SECRET_KEY'),
        ])->get("https://api.paystack.co/transaction/verify/{$trxRef}");

        if ($response['data']['status'] === 'success') {
            $paymentAmount = $response['data']['amount'] / 100;

            // 1. Update invoice status
            $invoice->status = 'Paid';
            $invoice->save();

            // 2. Reduce customer balance
            $customer = $invoice->customer;
            $customer->balance -= $paymentAmount;
            $customer->save();

            // 3. Optional: Save payment record
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $paymentAmount,
                'method' => 'Paystack',
                'payment_date' => now(),
            ]);

            //return response()->json(['message' => 'Payment verified and recorded.']);
            return redirect('/#/invoices?payment=success');
        }
        return response()->json(['message' => 'Payment verification failed.'], 400);
    }


}
