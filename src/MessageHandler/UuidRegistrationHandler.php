<?php

declare(strict_types=1);

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;
use Yii;

#[AsMessageHandler]
final readonly class UuidRegistrationHandler
{
    public function __invoke(Uuid $uuid): void
    {
        file_put_contents(Yii::getAlias('@runtime/log/uuid.txt'), $uuid . PHP_EOL, FILE_APPEND);
    }
}
