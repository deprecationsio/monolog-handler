<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deprecationsio\Monolog\Client;

use Deprecationsio\Monolog\Context\Event;

interface DeprecationsioClientInterface
{
    /**
     * @param string $dsn
     * @param Event $event
     *
     * @return void
     */
    public function sendEvent($dsn, Event $event);
}
