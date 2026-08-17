<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultatFinalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public string $pdfContenuBase64,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Votre résultat final");
    }

    public function content(): Content
    {
        return new Content(view: 'admin.emails.resultat-final');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => base64_decode($this->pdfContenuBase64), "resultat-final-{$this->student->name}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}