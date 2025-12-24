<?php

use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $targetDate = now()->addDay()->toDateString();

    Loan::whereNull('returned_at')
        ->whereDate('return_date', '=', $targetDate)
        ->orderBy('id')
        ->get()
        ->each(function (Loan $loan) {
            SendBookDueNotification::dispatch($loan->id);
        });
})->dailyAt('08:00');
