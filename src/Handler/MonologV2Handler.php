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

use Monolog\Handler\HandlerInterface;

/**
 * Handler for Monolog 2.x
 */
class MonologV2Handler extends AbstractMonologHandler implements HandlerInterface
{
    public function isHandling(array $record): bool
    {
        return $this->isRecordValid($record);
    }

    /**
     * @param array $record
     * @return bool
     */
    protected function isRecordValid($record)
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
        $this->sendEventForRecords($records);
    }

    public function close(): void
    {
        // no-op (unused by deprecations.io)
    }
}
