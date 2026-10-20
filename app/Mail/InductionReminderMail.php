<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InductionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $percentage;

    public function __construct(User $user)
    {
        $this->user = $user;
        // Obtenemos el porcentaje calculado que ya tienes en el modelo
        $this->percentage = $user->induction_viewed_percentage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: Avance de tu Proceso de Inducción I-DEB',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.induction_reminder',
        );
    }
}