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

use DeprecationsIo\Monolog\Context\Event;

class CurlDeprecationsIoClient implements DeprecationsIoClientInterface
{
    public function sendEvent($dsn, Event $event)
    {
        $content = json_encode($event->toArray());

        $handle = curl_init();

        curl_setopt_array($handle, array(
            CURLOPT_POST => true,
            CURLOPT_URL => $dsn,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_TIMEOUT_MS => 1000,
            CURLOPT_RETURNTRANSFER => true,
        ));

        curl_exec($handle);
        curl_close($handle);
    }
}
