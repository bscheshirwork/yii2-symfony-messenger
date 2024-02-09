<?php

declare(strict_types=1);

use App\Serializer\SymfonyMessageSerializer;
use yii\queue\amqp_interop\Queue;

$db = require __DIR__ . '/db.php';

return [
    'id' => 'yii2-symfony-messenger',
    'basePath' => dirname(__DIR__),
    'runtimePath' => '@app/var',
    'controllerNamespace' => 'App\Command',
    'bootstrap' => [
        'queue', // The component registers its own console commands
    ],
    'components' => [
        'queue' => [
            'class' => Queue::class,
            'host' => getenv('RABBITMQ_HOST'),
            'port' => getenv('RABBITMQ_PORT'),
            'user' => getenv('RABBITMQ_USER'),
            'password' => getenv('RABBITMQ_PASSWORD'),
            'exchangeName' => 'messages',
            'queueName' => 'messages',
            'driver' => Queue::ENQUEUE_AMQP_LIB,
            'strictJobType' => false,
            'serializer' => SymfonyMessageSerializer::class,
        ],
    ],
];