<?php

declare(strict_types=1);

namespace App\Queue;

use App\MessageHandler\MessageHandler;
use yii\queue\amqp_interop\Queue;

final class CliQueue extends Queue
{
    public $strictJobType = false;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->messageHandler = new MessageHandler();
    }

    public function execute($id, $message, $ttr, $attempt, $workerPid): bool
    {
        return ($this->messageHandler)($id, $message, (int)$ttr, (int)$attempt);
    }


}