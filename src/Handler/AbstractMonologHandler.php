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

use DeprecationsIo\Monolog\Client\CurlDeprecationsIoClient;
use DeprecationsIo\Monolog\Client\DeprecationsIoClientInterface;
use DeprecationsIo\Monolog\Context\Event;
use DeprecationsIo\Monolog\Context\EventFactory;

abstract class AbstractMonologHandler
{
    private $client;
    private $dsn;
    private $eventFactory;

    public function __construct($dsn, DeprecationsIoClientInterface $client = null)
    {
        $this->dsn = $dsn;
        $this->client = $client ?: new CurlDeprecationsIoClient();
    }

    /**
     * @param mixed $record
     * @return bool
     */
    abstract protected function shouldLog($record);

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    protected function isDeprecationRecord($level, $message, array $context)
    {
        // Symfony 2.8-3.4
        if (isset($context['type']) && ($context['type'] === E_USER_DEPRECATED || $context['type'] === E_DEPRECATED)) {
            return true;
        }

        if (isset($context['code']) && ($context['code'] === E_USER_DEPRECATED || $context['code'] === E_DEPRECATED)) {
            return true;
        }

        // Symfony 4.0+
        if (!empty($context['exception'])
            && $context['exception'] instanceof \ErrorException
            && ($context['exception']->getSeverity() === E_USER_DEPRECATED || $context['exception']->getSeverity() === E_DEPRECATED)) {
            return true;
        }

        // Generic
        return false !== strpos($message, 'deprecated');
    }

    protected function sendEventForRecords(array $records)
    {
        if (!$this->eventFactory) {
            $this->eventFactory = new EventFactory();
        }

        $event = $this->eventFactory->createEvent(PHP_SAPI);

        foreach ($records as $record) {
            if ($this->shouldLog($record)) {
                $this->addRecordToEvent($event, $record);
            }
        }

        if ($event->hasDeprecations()) {
            $this->client->sendEvent($this->dsn, $event);
        }
    }

    private function addRecordToEvent(Event $event, $record)
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception) {
            $event->addDeprecation(
                $record['context']['exception']->getMessage(),
                $record['context']['exception']->getFile(),
                $record['context']['exception']->getLine(),
                $record['context']['exception']->getTrace()
            );

            return;
        }

        $trace = array();
        if (!empty($record['context']['stack'])) {
            $trace = $record['context']['stack'];
        } elseif (!empty($record['context']['trace'])) {
            $trace = $record['context']['trace'];
        }

        $event->addDeprecation(
            $record['message'],
            !empty($record['context']['file']) ? $record['context']['file'] : null,
            !empty($record['context']['line']) ? $record['context']['line'] : null,
            $trace
        );
    }
}
