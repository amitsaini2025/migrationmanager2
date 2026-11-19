<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadMigrateInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $inviteUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAddress(),
            subject: 'Complete your migration enquiry form',
        );
    }

    public function fromAddress(): Address
    {
        return new Address(
            (string) config('services.zoho.from.address', config('mail.info.address', 'info@bansalimmigration.com.au')),
            (string) config('services.zoho.from.name', config('mail.from.name', 'Bansal Immigration')),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead_migrate_invite',
        );
    }
}
