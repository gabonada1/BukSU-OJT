<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\Tenant;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Student $student,
        public string $plainPassword,
        protected TenantUrlGenerator $urlGenerator,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your BukSU Practicum Portal student account is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-credentials',
            with: [
                'tenant' => $this->tenant,
                'student' => $this->student,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $this->urlGenerator->loginUrl($this->tenant),
            ],
        );
    }
}
