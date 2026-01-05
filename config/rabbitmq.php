<?php
return [
    'host' => env('RABBITMQ_HOST', 'rabbitmq'),
    'port' => (int) env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'queues' => [
        'due' => env('RABBITMQ_DUE_QUEUE', 'due_notifications'),
        'cart' => env('RABBITMQ_CART_QUEUE', 'cart_engagement'),
    ],
    'consumer' => [
        'max_retries' => (int) env('RABBITMQ_MAX_RETRIES', 5),
        'prefetch' => (int) env('RABBITMQ_PREFETCH', 10),
    ],
];
