<?php

namespace App\Console\Commands;

use App\Services\RabbitMQPublisher;
use Illuminate\Console\Command;

class RabbitMQPublishCartTest extends Command
{
    protected $signature = 'rabbitmq:publish-cart-test {user_id}';

    protected $description = 'Publish a cart engagement message to RabbitMQ for testing.';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $publisher = app(RabbitMQPublisher::class);
        $queue = (string) config('rabbitmq.queues.cart', 'cart_engagement');

        $publisher->publish($queue, [
            'user_id' => $userId,
            'retries' => 0,
            'created_at' => now()->toIso8601String(),
        ]);

        $this->info("Published cart message to {$queue} for user_id={$userId}");

        return self::SUCCESS;
    }
}
