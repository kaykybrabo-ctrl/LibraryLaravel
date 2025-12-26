<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Services\Notifications\BookDueNotificationStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookDueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $loanId;

    public function __construct(int $loanId)
    {
        $this->loanId = $loanId;
    }

    public function handle(BookDueNotificationStrategy $strategy): void
    {
        $loan = Loan::with(['user', 'book'])->find($this->loanId);

        if (!$loan || !$loan->user || !$loan->book) {
            return;
        }

        $html = view('emails.book_due', [
            'loan' => $loan,
            'user' => $loan->user,
            'book' => $loan->book,
        ])->render();

        $strategy->notify($loan, $html);
    }
}
