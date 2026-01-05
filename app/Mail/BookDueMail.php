<?php
namespace App\Mail;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookDueMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function __construct(
        public Loan $loan,
        public User $user,
        public Book $book,
    ) {
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete de devolução - PedBook',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.book_due',
            with: [
                'loan' => $this->loan,
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
