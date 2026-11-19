<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\CheckinLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadMigrateInviteReceptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Admin $lead,
        public CheckinLog $visit,
        public string $waitingUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->fromAddress(),
            subject: 'New Migration Enquiry — Waiting (In Person)',
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
            view: 'emails.lead_migrate_invite_reception',
        );
    }
}
