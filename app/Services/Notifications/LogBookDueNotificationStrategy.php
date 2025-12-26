<?php

namespace App\Services\Notifications;

use App\Models\Loan;
use Illuminate\Support\Facades\Log;

class LogBookDueNotificationStrategy implements BookDueNotificationStrategy
{
    public function notify(Loan $loan, string $html): void
    {
        Log::info('book_due_notification', [
            'user_id' => $loan->user_id,
            'book_id' => $loan->book_id,
            'return_date' => $loan->return_date ? $loan->return_date->toDateString() : null,
            'payload' => $html,
        ]);
    }
}
