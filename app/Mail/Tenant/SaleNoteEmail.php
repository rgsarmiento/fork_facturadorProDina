<?php

namespace App\Mail\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaleNoteEmail extends Mailable
{
    use Queueable, SerializesModels;
    use StorageDocument;

    public $company;
    public $document;

    public function __construct($company, $document)
    {
        $this->company = $company;
        $this->document = $document;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = $this->getStorage($this->document->filename, 'sale_note');

        return $this->subject('Envio de Factura POS')
                    ->from(config('mail.username'), 'Factura POS')
                    ->view('tenant.templates.email.document')
                    ->attachData($pdf, $this->document->filename.'.pdf');
    }
}
