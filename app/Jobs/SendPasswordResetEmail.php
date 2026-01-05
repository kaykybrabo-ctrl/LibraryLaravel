<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(
        public int $userId,
        public string $url,
    ) {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user || empty($user->email)) {
            return;
        }

        Mail::to($user->email)->send(new ResetPasswordMail($user, $this->url));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SendPasswordResetEmail failed', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
