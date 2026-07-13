<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use App\Models\User;

class RatingSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $ratingDetails;
    public $overallRemark;
    public $raterName;
    public $averageRating;

    /**
     * Create a new message instance.
     */
    public function __construct(User $staff, array $ratingDetails = [], string $overallRemark = '', string $raterName = '', float $averageRating = 0)
    {
        $this->staff = $staff;
        $this->ratingDetails = $ratingDetails;
        $this->overallRemark = $overallRemark;
        $this->raterName = $raterName;
        $this->averageRating = $averageRating;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('RATING_MAIL_FROM_ADDRESS', 'ho@sadhumargi.com'), env('RATING_MAIL_FROM_NAME', 'Sadhumargi HO')),
            subject: 'New Rating Submitted for ' . $this->staff->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rating_submitted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
