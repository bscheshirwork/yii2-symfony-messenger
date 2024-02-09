<?php

declare(strict_types=1);

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Yii;

#[AsMessageHandler]
final readonly class UuidRegistrationHandler
{
    public function __invoke(UuidRegistrationJob $uuidRegistrationJob): void
    {
        $uuid = $uuidRegistrationJob->uuid;
        file_put_contents(Yii::getAlias('@runtime/log/uuid.txt'), $uuid . PHP_EOL, FILE_APPEND);
    }
}
