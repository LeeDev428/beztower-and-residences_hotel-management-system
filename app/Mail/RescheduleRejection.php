<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RescheduleRejection extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public ?string $requestedDate;

    public function __construct(Booking $booking, ?string $requestedDate = null)
    {
        $this->booking = $booking;
        $this->requestedDate = $requestedDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Reschedule Request Update - Bez Tower & Residences',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reschedule-rejection',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
