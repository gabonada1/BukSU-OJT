<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantAdminCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $adminName,
        public string $email,
        public string $password,
        protected TenantUrlGenerator $urlGenerator,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Internship Coordinator account is ready | '.$this->tenant->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-admin-credentials',
            with: [
                'tenant' => $this->tenant,
                'adminName' => $this->adminName,
                'email' => $this->email,
                'password' => $this->password,
                'loginUrl' => $this->urlGenerator->loginUrl($this->tenant),
            ],
        );
    }
}
