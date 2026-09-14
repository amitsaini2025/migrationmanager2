<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ClientDetailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAddress(),
            subject: 'Please verify your Personal & Visa details',
        );
    }

    public function fromAddress(): Address
    {
        return new Address(
            (string) config('services.zoho.from.address', config('mail.info.address', 'info@bansalimmigration.com.au')),
            (string) config('services.zoho.from.name', config('mail.from.name', 'Bansal Immigration')),
        );
    }

    /**
     * Disable SendGrid click-tracking so the unique verification token URL stays intact.
     */
    public function headers(): Headers
    {
        return new Headers(text: [
            'X-SMTPAPI' => json_encode([
                'filters' => [
                    'clicktrack' => [
                        'settings' => [
                            'enable' => 0,
                        ],
                    ],
                ],
            ]),
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client_detail_verification',
        );
    }
}
