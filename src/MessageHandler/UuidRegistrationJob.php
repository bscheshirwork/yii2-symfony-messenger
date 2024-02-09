<?php

declare(strict_types=1);

namespace App\MessageHandler;

use Symfony\Component\Uid\Uuid;
use Yii;
use yii\queue\JobInterface;

final readonly class UuidRegistrationJob implements JobInterface
{
    public function __construct(public Uuid $uuid)
    {
    }

    public function execute($queue): void
    {
        $uuid = $this->uuid;
        file_put_contents(Yii::getAlias('@runtime/log/uuid.txt'), $uuid . PHP_EOL, FILE_APPEND);
    }
}
