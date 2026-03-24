<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckinReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Your Upcoming Stay at Bez Tower and Residences',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.checkin-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
