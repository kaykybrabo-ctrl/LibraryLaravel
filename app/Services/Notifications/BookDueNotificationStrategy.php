<?php

namespace App\Services\Notifications;

use App\Models\Loan;

interface BookDueNotificationStrategy
{
    public function notify(Loan $loan, string $html): void;
}
