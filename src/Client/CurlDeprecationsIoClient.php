<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeprecationsIo\Monolog\Client;

class CurlDeprecationsIoClient implements DeprecationsIoClientInterface
{
    public function sendEvent($dsn, array $event)
    {
        var_dump($dsn, $event);
    }
}
