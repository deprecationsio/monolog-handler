<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Deprecationsio\Monolog\Client;

use Deprecationsio\Monolog\Client\DeprecationsIoClientInterface;
use Deprecationsio\Monolog\Context\Event;

class MockDeprecationsIoClient implements DeprecationsIoClientInterface
{
    public $events;

    public function sendEvent($dsn, Event $event)
    {
        if (!$this->events) {
            $this->events = array();
        }

        $this->events[] = array(
            'dsn' => $dsn,
            'event' => $event->toArray(),
        );
    }
}
