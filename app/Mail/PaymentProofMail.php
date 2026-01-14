<?php

namespace App\Mail;

use App\Models\SupplierInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentProofMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(SupplierInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Comprovativo de Pagamento - Fatura ' . $this->invoice->number)
                      ->view('emails.payment-proof')
                      ->with([
                          'invoice' => $this->invoice,
                      ]);

        // Attach payment proof if exists
        if ($this->invoice->payment_proof_path && Storage::disk('documents')->exists($this->invoice->payment_proof_path)) {
            $path = Storage::disk('documents')->path($this->invoice->payment_proof_path);
            $email->attach($path, [
                'as' => 'Comprovativo_Pagamento.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
