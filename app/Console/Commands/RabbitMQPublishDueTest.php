<?php

namespace App\Console\Commands;

use App\Services\RabbitMQPublisher;
use Illuminate\Console\Command;

class RabbitMQPublishDueTest extends Command
{
    protected $signature = 'rabbitmq:publish-due-test {loan_id}';

    protected $description = 'Publish a due notification message to RabbitMQ for testing.';

    public function handle(): int
    {
        $loanId = (int) $this->argument('loan_id');
        $publisher = app(RabbitMQPublisher::class);
        $queue = (string) config('rabbitmq.queues.due', 'due_notifications');

        $publisher->publish($queue, [
            'loan_id' => $loanId,
            'retries' => 0,
            'created_at' => now()->toIso8601String(),
        ]);

        $this->info("Published due message to {$queue} for loan_id={$loanId}");

        return self::SUCCESS;
    }
}
