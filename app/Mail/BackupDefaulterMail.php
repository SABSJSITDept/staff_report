<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupDefaulterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;

    public function __construct($staff)
    {
        $this->staff = $staff;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Backup Pending Alert',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.backup-defaulter',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
