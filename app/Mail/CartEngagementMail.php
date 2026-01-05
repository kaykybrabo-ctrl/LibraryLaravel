<?php
namespace App\Mail;

use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartEngagementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(
        public User $user,
        public Book $book,
    ) {
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu carrinho está te esperando - PedBook',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cart_engagement',
            with: [
                'user' => $this->user,
                'book' => $this->book,
            ],
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
