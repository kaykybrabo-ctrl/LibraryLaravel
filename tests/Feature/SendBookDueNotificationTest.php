<?php

use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Support\Facades\Log;

it('logs a book due notification with correct context', function () {
    $loan = Loan::factory()->create();

    Log::shouldReceive('info')
        ->once()
        ->withArgs(function ($message, $context) use ($loan) {
            return $message === 'book_due_notification'
                && ($context['user_id'] ?? null) === $loan->user_id
                && ($context['book_id'] ?? null) === $loan->book_id;
        });

    $job = new SendBookDueNotification($loan->id);
    $job->handle(app(\App\Services\Notifications\BookDueNotificationStrategy::class));
});
