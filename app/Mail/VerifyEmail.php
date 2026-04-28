<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;
    public string $name;

    public function __construct(string $verifyUrl, string $name)
    {
        $this->verifyUrl = $verifyUrl;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Your Email - TechShop');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify_email');
    }
}
