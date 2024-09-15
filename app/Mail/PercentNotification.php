<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PercentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $interval;

    public $percent;

    public $percentChange;

    /**
     * Create a new message instance.
     */
    public function __construct($interval, $percent, $percentChange)
    {
        $this->interval = $interval;
        $this->percent = $percent;
        $this->percentChange = $percentChange;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bitcoin Price Notification (%)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.percent_alert',
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
