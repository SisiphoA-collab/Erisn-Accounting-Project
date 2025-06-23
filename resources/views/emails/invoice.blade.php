@component('mail::message')
# Invoice #{{ $invoice->id }}

Dear {{ $invoice->customer->name }},

Thank you for your purchase! Please find attached your invoice.
Below are the invoice details:<br/>
--Payment Due Date: {{ $invoice->due_date }}<br/>
--Total Amount Due: R{{ $invoice->amount }}<br/>

Please make payment by <strong>{{ $invoice->due_date }}</strong> to avoid late fees.

<p>You can complete your payment by clicking the link below:<br/>
<span class="link-primary">{{ $url }}</span></p>


Best regards,<br/>
{{ $invoice->customer->company->name }}
@endcomponent