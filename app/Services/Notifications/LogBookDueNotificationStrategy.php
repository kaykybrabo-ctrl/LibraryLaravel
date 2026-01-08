<?php
namespace App\Services\Notifications;
use App\Models\Loan;
use Illuminate\Support\Facades\Log;

/**
 * Book due notification strategy that logs notifications instead of sending them.
 */
class LogBookDueNotificationStrategy implements BookDueNotificationStrategy
{
    /**
     * Log the due notification payload for the given loan.
     */
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
