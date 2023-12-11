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

abstract class AbstractMonologHandler
{
    private $client;
    private $dsn;
    private $eventFactory;

    public function __construct(DeprecationsIoClientInterface $client, $dsn)
    {
        $this->client = $client;
        $this->dsn = $dsn;
    }

    protected function sendEventForRecords(array $records)
    {
        if (!$this->eventFactory) {
            $this->eventFactory = new EventFactory();
        }

        $event = $this->eventFactory->createEvent(PHP_SAPI);

        foreach ($records as $record) {
            if (!$this->isRecordValid($record)) {
                continue;
            }

            $event->addDeprecation($record['context']['exception']);
        }

        $this->client->sendEvent($this->dsn, $event);
    }

    /**
     * @param mixed $record
     * @return bool
     */
    abstract protected function isRecordValid($record);
}
