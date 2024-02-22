<?php

declare(strict_types=1);

namespace App\MessageHandler;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use SplFileInfo;
use SplObjectStorage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final readonly class HandlersLocator
{
    private SplObjectStorage $reflections;

    private static function searchClasses(string $namespace, string $namespacePath): array
    {
        $classes = [];

        /**
         * @var RecursiveDirectoryIterator $iterator
         * @var SplFileInfo $item
         */
        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($namespacePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            if ($item->isDir()) {
                $nextPath = $iterator->current()->getPathname();
                $nextNamespace = $namespace . '\\' . $item->getFilename();
                foreach (self::searchClasses($nextNamespace, $nextPath) as $nestedClass) {
                    $classes[] = $nestedClass;
                }
                continue;
            }
            if ($item->isFile() && $item->getExtension() === 'php') {
                $class = $namespace . '\\' . $item->getBasename('.php');
                if (!class_exists($class)) {
                    continue;
                }
                $classes[] = $class;
            }
        }

        return $classes;
    }

    /**
     * @throws ReflectionException
     */
    public function __construct(
        private string $namespace = __NAMESPACE__,
        private string $location = __DIR__,
    )
    {
        $this->reflections = new SplObjectStorage;
        foreach (self::searchClasses($this->namespace, $this->location) as $className) {
            $reflectionClass = new ReflectionClass($className);
            $attributes = $reflectionClass->getAttributes(AsMessageHandler::class);
            if ($attributes) {
                $this->reflections->attach($reflectionClass, $attributes);
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    public function __invoke(SplObjectStorage $handlers): void
    {
        foreach ($this->reflections as $reflectionClass) {
            /** @var ReflectionClass $reflectionClass */
            try {
                $reflectionInvoke = $reflectionClass->getMethod('__invoke');
            } catch (ReflectionException) {
                continue;
            }
            $reflectionInvokeParameters = $reflectionInvoke->getParameters();
            $reflectionType = $reflectionInvokeParameters[0]?->getType();
            if ($reflectionType instanceof ReflectionNamedType) {
                $handlers->attach($reflectionClass->newInstance(), $reflectionType->getName());
            }
        }
    }

}