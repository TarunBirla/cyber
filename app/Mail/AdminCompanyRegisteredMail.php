<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCompanyRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyUser;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct($companyUser, $company)
    {
        $this->companyUser = $companyUser;
        $this->company = $company;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Company Registered: ' . ($this->companyUser->name),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_company_registered',
        );
    }
}
