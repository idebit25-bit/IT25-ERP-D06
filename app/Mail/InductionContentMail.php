<?php

namespace App\Mail;

use App\Models\Page;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InductionContentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public Page $page) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Contenido disponible en tu inducción I-DEB');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.induction_content');
    }
}
