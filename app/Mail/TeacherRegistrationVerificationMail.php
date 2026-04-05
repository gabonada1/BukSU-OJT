<?php

namespace App\Mail;

use App\Models\Supervisor;
use App\Models\Tenant;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherRegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Supervisor $teacher,
        protected TenantUrlGenerator $urlGenerator,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your University Practicum company supervisor registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher-registration-verification',
            with: [
                'tenant' => $this->tenant,
                'teacher' => $this->teacher,
                'verificationUrl' => $this->urlGenerator->verificationUrl(
                    $this->tenant,
                    (string) $this->teacher->email_verification_token
                ),
                'loginUrl' => $this->urlGenerator->loginUrl($this->tenant),
            ],
        );
    }
}
