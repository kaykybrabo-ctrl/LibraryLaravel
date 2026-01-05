<?php
namespace App\Services\Notifications;
use App\Mail\BookDueMail;
use App\Models\Loan;
use Illuminate\Support\Facades\Mail;
class MailBookDueNotificationStrategy implements BookDueNotificationStrategy
{
    public function notify(Loan $loan, string $html): void
    {
        if (!$loan->user || empty($loan->user->email)) {
            return;
        }
        Mail::to($loan->user->email)->queue(new BookDueMail($loan, $loan->user, $loan->book));
    }
}
