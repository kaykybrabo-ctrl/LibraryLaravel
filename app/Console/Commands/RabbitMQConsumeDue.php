<?php

namespace App\Console\Commands;

use App\Jobs\SendBookDueNotification;
use App\Services\RabbitMQPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeDue extends Command
{
    protected $signature = 'rabbitmq:consume-due';

    protected $description = 'Consume RabbitMQ due notifications queue and dispatch jobs.';

    public function handle(): int
    {
        $config = config('rabbitmq');
        $queue = (string) ($config['queues']['due'] ?? 'due_notifications');
        $maxRetries = (int) ($config['consumer']['max_retries'] ?? 5);
        $prefetch = (int) ($config['consumer']['prefetch'] ?? 10);

        $this->info("[rabbitmq] consuming due queue: {$queue}");

        $conn = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost'],
        );

        $channel = $conn->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(null, $prefetch, null);

        $publisher = app(RabbitMQPublisher::class);

        $callback = function (AMQPMessage $msg) use ($queue, $maxRetries, $publisher) {
            $body = (string) $msg->getBody();
            $payload = json_decode($body, true);

            if (!is_array($payload) || empty($payload['loan_id'])) {
                Log::warning('[rabbitmq] invalid message payload for due queue', ['body' => $body]);
                $msg->ack();
                return;
            }

            $loanId = (int) $payload['loan_id'];
            $retries = (int) ($payload['retries'] ?? 0);

            try {
                SendBookDueNotification::dispatch($loanId);
                $msg->ack();
            } catch (\Throwable $e) {
                Log::error('[rabbitmq] error processing due message', [
                    'loan_id' => $loanId,
                    'retries' => $retries,
                    'error' => $e->getMessage(),
                ]);

                if ($retries < $maxRetries) {
                    $payload['retries'] = $retries + 1;
                    $payload['last_error'] = $e->getMessage();
                    $payload['retried_at'] = now()->toIso8601String();
                    $publisher->publish($queue, $payload);
                }

                $msg->ack();
            }
        };

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $conn->close();

        return self::SUCCESS;
    }
}
