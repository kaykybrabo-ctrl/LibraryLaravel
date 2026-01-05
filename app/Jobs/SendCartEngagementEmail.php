<?php
namespace App\Jobs;
use App\Mail\CartEngagementMail;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
class SendCartEngagementEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public int $timeout = 30;
    public function backoff(): array
    {
        return [10, 30, 60];
    }
    public function __construct(public int $userId)
    {
    }
    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user || empty($user->email) || !empty($user->is_admin)) {
            return;
        }

        $cooldownMinutes = (int) env('CART_ENGAGEMENT_COOLDOWN_MINUTES', 10);
        $cacheKey = 'cart_engagement:last_sent:' . (int) $user->id;
        if (Cache::has($cacheKey)) {
            return;
        }

        $item = CartItem::where('user_id', $user->id)
            ->with('book')
            ->orderByDesc('updated_at')
            ->first();
        if (!$item || !$item->book) {
            return;
        }

        Cache::put($cacheKey, now()->toIso8601String(), now()->addMinutes($cooldownMinutes));
        Mail::to($user->email)->queue(new CartEngagementMail($user, $item->book));
    }
    public function failed(Throwable $exception): void
    {
        Log::error('SendCartEngagementEmail failed', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
