<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $filePath;
    public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice, $filePath, $url)
    {
        $this->invoice = $invoice;
        $this->filePath = $filePath;
        $this->url = $url;
    }


    public function build()
    {
        if (file_exists($this->filePath)) {
            return $this->from('vuyodlamini81@gmail.com',name: $this->invoice->customer->company->name)
                ->subject('Your Invoice #' . $this->invoice->id)
                ->markdown('emails.invoice', [
                    'invoice' => $this->invoice,
                    'url' => $this->url,
                ])
                ->attach($this->filePath, [
                    'as' => $this->invoice->customer->name . '_invoice#' . $this->invoice->id . '.pdf',
                    'mime' => 'application/pdf',
                ]);
        } else {
            Log::error("Invoice PDF not found: " . $this->filePath);
            return $this->from('vuyodlamini81@gmail.com')
                ->subject('Your Invoice #' . $this->invoice->id)
                ->markdown('emails.invoice', [
                    'invoice' => $this->invoice,
                    'url' => $this->url,
                ]);
        }
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Your Invoice #' . $this->invoice->id
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
