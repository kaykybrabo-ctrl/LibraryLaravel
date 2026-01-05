<?php
use App\Jobs\SendBookDueNotification;
use App\Models\Loan;
use Illuminate\Support\Facades\Cache;
use App\Services\Notifications\BookDueNotificationStrategy;
use Illuminate\Support\Carbon;
it('dispatches due notification via strategy and caches per day', function () {
    Carbon::setTestNow('2025-01-10 10:00:00');
    Cache::flush();
    $loan = Loan::factory()->create();
    $cacheKey = 'book_due:last_sent:' . (int) $loan->id . ':' . now()->toDateString();
    Cache::forget($cacheKey);

    $strategy = \Mockery::mock(BookDueNotificationStrategy::class);
    $strategy->shouldReceive('notify')
        ->once()
        ->withArgs(function ($passedLoan, $html) use ($loan) {
            return (int) $passedLoan->id === (int) $loan->id && is_string($html);
        });

    $job = new SendBookDueNotification($loan->id);
    $job->handle($strategy);

    expect(Cache::has($cacheKey))->toBeTrue();

    $strategy2 = \Mockery::mock(BookDueNotificationStrategy::class);
    $strategy2->shouldReceive('notify')->never();
    $job->handle($strategy2);
});
