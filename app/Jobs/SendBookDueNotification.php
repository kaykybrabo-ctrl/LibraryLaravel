<?php
namespace App\Jobs;
use App\Models\Loan;
use App\Services\Notifications\BookDueNotificationStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
class SendBookDueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public int $timeout = 30;
    public function backoff(): array
    {
        return [10, 30, 60];
    }
    public int $loanId;
    public function __construct(int $loanId)
    {
        $this->loanId = $loanId;
    }
    public function handle(BookDueNotificationStrategy $strategy): void
    {
        $loan = Loan::with(['user', 'book'])->find($this->loanId);
        if (!$loan || !$loan->user || !$loan->book) {
            return;
        }

        $cacheKey = 'book_due:last_sent:' . (int) $loan->id . ':' . now()->toDateString();
        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, now()->toIso8601String(), now()->endOfDay());
        $strategy->notify($loan, '');
    }
    public function failed(Throwable $exception): void
    {
        Log::error('SendBookDueNotification failed', [
            'loan_id' => $this->loanId,
            'error' => $exception->getMessage(),
        ]);
    }
}
