<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Loan;
use App\Models\CartItem;
use App\Jobs\SendCartEngagementEmail;
use App\Services\RabbitMQPublisher;
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $daysAhead = [2, 1];
            foreach ($daysAhead as $days) {
                $targetDate = now()->addDays($days)->toDateString();
                Log::info('[schedule] due-loans dispatch start', [
                    'target_date' => $targetDate,
                    'days_ahead' => $days,
                    'timezone' => config('app.timezone', 'UTC')
                ]);
                try {
                    $queue = config('rabbitmq.queues.due', 'due_notifications');
                    $publisher = app(RabbitMQPublisher::class);
                    Loan::whereNull('returned_at')
                        ->whereDate('return_date', '=', $targetDate)
                        ->orderBy('id')
                        ->chunk(100, function ($loans) use ($publisher, $queue, $days) {
                            foreach ($loans as $loan) {
                                try {
                                    $publisher->publish($queue, [
                                        'loan_id' => $loan->id,
                                        'retries' => 0,
                                        'created_at' => now()->toIso8601String(),
                                        'days_ahead' => $days,
                                    ]);
                                } catch (\Throwable $e) {
                                    Log::error('[schedule] failed to dispatch SendBookDueNotification', [
                                        'loan_id' => $loan->id,
                                        'days_ahead' => $days,
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString()
                                    ]);
                                }
                            }
                        });
                    Log::info('[schedule] due-loans dispatch end', ['target_date' => $targetDate, 'days_ahead' => $days]);
                } catch (\Throwable $e) {
                    Log::error('[schedule] due-loans failed', [
                        'target_date' => $targetDate,
                        'days_ahead' => $days,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        })
        ->dailyAt('14:15')
        ->timezone(config('app.timezone', 'America/Sao_Paulo'))
        ->description('Send due date notifications for books');

        $schedule->call(function () {
            Log::info('[schedule] cart-engagement dispatch start', [
                'timezone' => config('app.timezone', 'UTC')
            ]);
            try {
                $queue = config('rabbitmq.queues.cart', 'cart_engagement');
                $publisher = app(RabbitMQPublisher::class);
                CartItem::query()
                    ->select('user_id')
                    ->where('quantity', '>', 0)
                    ->groupBy('user_id')
                    ->orderBy('user_id')
                    ->chunk(100, function ($users) use ($publisher, $queue) {
                        foreach ($users as $row) {
                            try {
                                $publisher->publish($queue, [
                                    'user_id' => (int) $row->user_id,
                                    'retries' => 0,
                                    'created_at' => now()->toIso8601String(),
                                ]);
                            } catch (\Throwable $e) {
                                Log::error('[schedule] failed to publish cart engagement message', [
                                    'user_id' => (int) $row->user_id,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                        }
                    });
                Log::info('[schedule] cart-engagement dispatch end');
            } catch (\Throwable $e) {
                Log::error('[schedule] cart-engagement failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })
        ->dailyAt('09:00')
        ->timezone(config('app.timezone', 'America/Sao_Paulo'))
        ->description('Send cart engagement emails');
        $schedule->command('queue:work --tries=3 --timeout=60 --sleep=3 --max-time=3600')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->timezone(config('app.timezone', 'America/Sao_Paulo'))
            ->description('Process queue jobs');
        $schedule->command('queue:retry all')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground()
            ->timezone(config('app.timezone', 'America/Sao_Paulo'))
            ->description('Retry failed queue jobs');
    }
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
