<?php
use App\Models\Loan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendBookDueNotification;
use App\Jobs\SendCartEngagementEmail;
use App\Services\RabbitMQPublisher;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Book;
Artisan::command('rabbitmq:consume-due', function () {
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
});
Artisan::command('rabbitmq:consume-cart', function () {
     $config = config('rabbitmq');
     $queue = (string) ($config['queues']['cart'] ?? 'cart_engagement');
     $maxRetries = (int) ($config['consumer']['max_retries'] ?? 5);
     $prefetch = (int) ($config['consumer']['prefetch'] ?? 10);
     $this->info("[rabbitmq] consuming cart queue: {$queue}");
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
         if (!is_array($payload) || empty($payload['user_id'])) {
             Log::warning('[rabbitmq] invalid message payload for cart queue', ['body' => $body]);
             $msg->ack();
             return;
         }
         $userId = (int) $payload['user_id'];
         $retries = (int) ($payload['retries'] ?? 0);
         try {
             SendCartEngagementEmail::dispatch($userId);
             $msg->ack();
         } catch (\Throwable $e) {
             Log::error('[rabbitmq] error processing cart message', [
                 'user_id' => $userId,
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
 });
Artisan::command('rabbitmq:test-prepare', function () {
    $user = User::where('is_admin', false)->orderBy('id')->first();
    $book = Book::orderBy('id')->first();
    if (!$user || !$book) {
        $this->error('Missing user or book to prepare test data');
        return;
    }
    $loan = Loan::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'loan_date' => now()->toDateString(),
        'return_date' => now()->addDay()->toDateString(),
        'returned_at' => null,
    ]);
    CartItem::updateOrCreate(
        ['user_id' => $user->id, 'book_id' => $book->id],
        ['quantity' => 1]
    );
    $this->info('Prepared test data');
    $this->line('loan_id=' . $loan->id);
    $this->line('user_id=' . $user->id);
    $this->line('book_id=' . $book->id);
});
Artisan::command('rabbitmq:publish-due-test {loan_id}', function () {
    $loanId = (int) $this->argument('loan_id');
    $publisher = app(RabbitMQPublisher::class);
    $queue = (string) config('rabbitmq.queues.due', 'due_notifications');
    $publisher->publish($queue, [
        'loan_id' => $loanId,
        'retries' => 0,
        'created_at' => now()->toIso8601String(),
    ]);
    $this->info("Published due message to {$queue} for loan_id={$loanId}");
});
Artisan::command('rabbitmq:publish-cart-test {user_id}', function () {
    $userId = (int) $this->argument('user_id');
    $publisher = app(RabbitMQPublisher::class);
    $queue = (string) config('rabbitmq.queues.cart', 'cart_engagement');
    $publisher->publish($queue, [
        'user_id' => $userId,
        'retries' => 0,
        'created_at' => now()->toIso8601String(),
    ]);
    $this->info("Published cart message to {$queue} for user_id={$userId}");
});
