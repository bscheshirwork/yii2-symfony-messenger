<?php

namespace App\Serializer;

use Symfony\Component\Messenger\Envelope;
use yii\helpers\VarDumper;
use yii\queue\InvalidJobException;
use yii\queue\JobInterface;
use yii\queue\serializers\PhpSerializer;

final class SymfonyMessageSerializer extends PhpSerializer
{
    /**
     * @throws InvalidJobException
     */
    public function unserialize($serialized): JobInterface
    {
        $envelope = parent::unserialize($serialized);

        if ($envelope instanceof Envelope) {
            return $envelope->getMessage();
        }

        throw new InvalidJobException($serialized, sprintf(
            'envelope message must be a JobInterface instance instead of %s.',
            VarDumper::dumpAsString($envelope)
        ));
    }
}
