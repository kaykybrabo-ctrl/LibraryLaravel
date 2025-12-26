<?php

use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Support\Facades\Log;

it('logs a book due notification with correct context', function () {
    $loan = Loan::factory()->create();

    Log::fake();

    $job = new SendBookDueNotification($loan->id);
    $job->handle();

    Log::assertLogged('info', function ($log) use ($loan) {
        return $log->message === 'book_due_notification'
            && ($log->context['user_id'] ?? null) === $loan->user_id
            && ($log->context['book_id'] ?? null) === $loan->book_id;
    });
});
