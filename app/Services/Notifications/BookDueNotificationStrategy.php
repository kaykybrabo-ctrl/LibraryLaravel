<?php
namespace App\Services\Notifications;
use App\Models\Loan;

/**
 * Strategy contract for sending book due notifications.
 */
interface BookDueNotificationStrategy
{
    /**
     * Send a notification for a given loan and rendered HTML content.
     */
    public function notify(Loan $loan, string $html): void;
}
