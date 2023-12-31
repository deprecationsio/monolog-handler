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

use Monolog\Handler\HandlerInterface;

/**
 * Handler for Monolog 2.x
 */
class MonologV2Handler extends AbstractMonologHandler implements HandlerInterface
{
    public function handle(array $record): bool
    {
        $this->handleBatch(array($record));

        return false;
    }

    public function handleBatch(array $records): void
    {
        $this->sendEventForRecords($records);
    }

    public function isHandling(array $record): bool
    {
        // Always true to receive all records and accept them during handling
        return true;
    }

    public function close(): void
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
