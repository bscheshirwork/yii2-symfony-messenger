<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Uid\Uuid;
use Yii;
use yii\console\Controller;

final class UuidController extends Controller
{
    public function actionIndex(): void
    {
        $message = Uuid::v7();
        $stamp = new BusNameStamp('messenger.bus.default');
        $envelope = new Envelope($message, [$stamp]);
        Yii::$app->queue->push($envelope);
    }
}