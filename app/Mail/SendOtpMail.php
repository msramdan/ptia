<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $expireInMinutes;

    /**
     * Create a new message instance.
     *
     * @param string $otpCode
     * @param int $expireInMinutes
     */
    public function __construct($otpCode, $expireInMinutes)
    {
        $this->otpCode = $otpCode;
        $this->expireInMinutes = $expireInMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi untuk ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            view: 'emails.send-otp',
        );
    }
}
