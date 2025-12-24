<?php

namespace App\Jobs;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookDueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $loanId;

    public function __construct(int $loanId)
    {
        $this->loanId = $loanId;
    }

    public function handle(): void
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

        Log::info('book_due_notification', [
            'user_id' => $loan->user_id,
            'book_id' => $loan->book_id,
            'return_date' => $loan->return_date ? $loan->return_date->toDateString() : null,
            'payload' => $html,
        ]);
    }
}
