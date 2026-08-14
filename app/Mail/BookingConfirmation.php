<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
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
            subject: __('تأكيد طلبك').' #'.$this->booking->id.' — GR Motors',
        );
    }

    public function content(): Content
    {
        $isEnglish = $this->booking->locale === 'en';

        return new Content(
            view: $isEnglish ? 'emails.booking_confirmation_en' : 'emails.booking_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
