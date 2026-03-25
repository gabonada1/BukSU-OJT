<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantSuspendedMail extends Mailable
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
            subject: 'College portal access suspended: '.$this->tenant->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-suspended',
            with: [
                'tenant' => $this->tenant,
                'recipientName' => $this->recipientName,
            ],
        );
    }
}
