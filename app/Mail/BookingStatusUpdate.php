<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public string $oldStatus;

    public string $newStatus;

    public function __construct(Booking $booking, string $oldStatus, string $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('تحديث حالة طلبك').' #'.$this->booking->id.' — GR Motors',
        );
    }

    public function content(): Content
    {
        $isEnglish = $this->booking->locale === 'en';

        return new Content(
            view: $isEnglish ? 'emails.booking_status_update_en' : 'emails.booking_status_update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
