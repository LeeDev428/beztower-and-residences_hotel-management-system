<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingExpired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['guest', 'room.roomType', 'rooms.roomType']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reservation Automatically Declined - Booking #'.$this->booking->booking_reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-expired',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
