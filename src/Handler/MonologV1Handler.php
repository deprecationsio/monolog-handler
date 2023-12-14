<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deprecationsio\Monolog\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;

/**
 * Handler for Monolog 1.x
 */
class MonologV1Handler extends AbstractMonologHandler implements HandlerInterface
{
    public function handle(array $record)
    {
        $this->handleBatch(array($record));

        return false;
    }

    public function handleBatch(array $records)
    {
        $this->sendEventForRecords($records);
    }

    public function isHandling(array $record)
    {
        // Always true to receive all records and accept them during handling
        return true;
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

    /**
     * @param array $record
     * @return bool
     */
    protected function shouldLog($record)
    {
        return $this->isDeprecationRecord($record['level'], $record['message'], $record['context']);
    }
}
