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
    public $consecutiveMissed;

    public function __construct($staff, $consecutiveMissed = null)
    {
        $this->staff = $staff;
        $this->consecutiveMissed = $consecutiveMissed;
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
