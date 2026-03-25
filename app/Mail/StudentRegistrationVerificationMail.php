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

class StudentRegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Student $student,
        protected TenantUrlGenerator $urlGenerator,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your BukSU Practicum Portal student registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-registration-verification',
            with: [
                'tenant' => $this->tenant,
                'student' => $this->student,
                'verificationUrl' => $this->urlGenerator->verificationUrl(
                    $this->tenant,
                    (string) $this->student->email_verification_token
                ),
                'loginUrl' => $this->urlGenerator->loginUrl($this->tenant),
            ],
        );
    }
}
