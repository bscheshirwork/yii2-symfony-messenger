<?php

declare(strict_types=1);

namespace App\MessageHandler;


use ReflectionException;
use SplObjectStorage;
use Symfony\Component\Messenger\Envelope;

final readonly class MessageHandler
{
    /**
     * @throws ReflectionException
     */
    public function __construct(
        private HandlersLocator  $handlersLocator = new HandlersLocator(),
        private SplObjectStorage $handlers = new SplObjectStorage(),
    )
    {
        ($this->handlersLocator)($this->handlers);
    }

    /**
     * @param string $id of a job message
     * @param string $message
     * @param int $ttr time to reserve
     * @param int $attempt number
     * @return bool
     */
    public function __invoke(string $id, string $message, int $ttr, int $attempt): bool
    {
        $envelope = unserialize($message, ['allowed_classes' => true]);

        if ($envelope instanceof Envelope) {
            $innerMessage = $envelope->getMessage();
            foreach ($this->handlers as $handler) {
                $handledClassName = $this->handlers->getInfo();
                if ($innerMessage instanceof $handledClassName) {
                    /** @var callable $handler */
                    $handler($innerMessage);

                    return true;
                }
            }
        }

        return false;
    }

}