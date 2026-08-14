<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 طلب حجز جديد #' . $this->booking->id . ' — ' . ($this->booking->client_name ?? 'عميل جديد'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_booking',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
