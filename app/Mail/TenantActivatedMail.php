<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $recipientName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'College portal access restored: '.$this->tenant->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-activated',
            with: [
                'tenant' => $this->tenant,
                'recipientName' => $this->recipientName,
            ],
        );
    }
}
