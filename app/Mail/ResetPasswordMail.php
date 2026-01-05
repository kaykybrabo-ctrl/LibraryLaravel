<?php
namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(
        public User $user,
        public string $url,
    ) {
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redefinição de senha - PedBook',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.reset-password',
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
