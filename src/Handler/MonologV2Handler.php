<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeprecationsIo\Monolog\Handler;

use DeprecationsIo\Monolog\Client\DeprecationsIoClientInterface;
use DeprecationsIo\Monolog\Context\EventFactory;
use Monolog\Handler\HandlerInterface;

/**
 * Handler for Monolog 2.x
 */
class MonologV2Handler implements HandlerInterface
{
    private $client;
    private $dsn;
    private $eventFactory;

    public function __construct(DeprecationsIoClientInterface $client, $dsn)
    {
        $this->client = $client;
        $this->dsn = $dsn;
    }

    public function isHandling(array $record): bool
    {
        return isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception;
    }

    public function handle(array $record): bool
    {
        $this->handleBatch(array($record));

        return false;
    }

    public function handleBatch(array $records): void
    {
        if (!$this->eventFactory) {
            $this->eventFactory = new EventFactory();
        }

        $event = $this->eventFactory->createEvent(PHP_SAPI);

        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            $event->addDeprecation($record['context']['exception']);
        }

        $this->client->sendEvent($this->dsn, $event);
    }

    public function close(): void
    {
        // no-op (unused by deprecations.io)
    }
}
