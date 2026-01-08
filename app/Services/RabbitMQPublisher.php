<?php
namespace App\Services;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Service responsible for publishing messages to RabbitMQ queues.
 */
class RabbitMQPublisher
{
    /**
     * Publish a JSON payload to a given queue.
     *
     * @param array<string,mixed> $payload
     */
    public function publish(string $queue, array $payload): void
    {
        $config = config('rabbitmq');
        $conn = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost'],
        );
        $channel = $conn->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );
        $channel->basic_publish($msg, '', $queue);
        $channel->close();
        $conn->close();
    }
}
