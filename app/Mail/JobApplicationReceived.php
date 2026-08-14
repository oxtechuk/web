<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class JobApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('طلب توظيف جديد: ') . $this->application->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job_application',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->application->resume_path) {
            return [
                Attachment::fromStorageDisk('public', $this->application->resume_path)
                    ->as('CV_' . str_replace(' ', '_', $this->application->name) . '.' . pathinfo($this->application->resume_path, PATHINFO_EXTENSION))
            ];
        }

        return [];
    }
}
