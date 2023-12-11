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
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;

/**
 * Handler for Monolog 1.x
 */
class MonologV1Handler implements HandlerInterface
{
    private $client;
    private $dsn;
    private $eventFactory;

    public function __construct(DeprecationsIoClientInterface $client, $dsn)
    {
        $this->client = $client;
        $this->dsn = $dsn;
    }

    public function isHandling(array $record)
    {
        return isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception;
    }

    public function handle(array $record)
    {
        $this->handleBatch(array($record));
    }

    public function handleBatch(array $records)
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

    public function pushProcessor($callback)
    {
        // no-op (unused by deprecations.io)
    }

    public function popProcessor()
    {
        // no-op (unused by deprecations.io)
    }

    public function setFormatter(FormatterInterface $formatter)
    {
        // no-op (unused by deprecations.io)
    }

    public function getFormatter()
    {
        // no-op (unused by deprecations.io)
    }
}
