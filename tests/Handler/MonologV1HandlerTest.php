<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\DeprecationsIo\Monolog\Handler;

use DeprecationsIo\Monolog\Handler\MonologV1Handler;
use Monolog\Logger;
use Tests\DeprecationsIo\Monolog\Client\MockDeprecationsIoClient;
use Tests\DeprecationsIo\Monolog\UnitTest;

class MonologV1HandlerTest extends UnitTest
{
    public function testHandleTypeKey()
    {
        if (1 !== $this->getMonologVersion()) {
            $this->markTestSkipped('Monolog v1 not installed.');
        }

        $client = new MockDeprecationsIoClient();
        $handler = new MonologV1Handler('https://ingest.deprecations.io/example?apikey=test', $client);

        $logger = new Logger('app', array($handler));
        $logger->notice('User Deprecated: deprecation example.', array(
            'type' => E_USER_DEPRECATED,
        ));

        $this->assertSame(
            'https://ingest.deprecations.io/example?apikey=test',
            $client->events[0]['dsn']
        );

        $this->assertSame(
            'User Deprecated: deprecation example.',
            $client->events[0]['event']['deprecations'][0]['message']
        );
    }

    public function testHandleCodeKey()
    {
        if (1 !== $this->getMonologVersion()) {
            $this->markTestSkipped('Monolog v1 not installed.');
        }

        $client = new MockDeprecationsIoClient();
        $handler = new MonologV1Handler('https://ingest.deprecations.io/example?apikey=test', $client);

        $logger = new Logger('app', array($handler));
        $logger->notice('User Deprecated: deprecation example.', array(
            'code' => E_DEPRECATED,
        ));

        $this->assertSame(
            'https://ingest.deprecations.io/example?apikey=test',
            $client->events[0]['dsn']
        );

        $this->assertSame(
            'User Deprecated: deprecation example.',
            $client->events[0]['event']['deprecations'][0]['message']
        );
    }

    public function testHandleExceptionKey()
    {
        if (1 !== $this->getMonologVersion()) {
            $this->markTestSkipped('Monolog v1 not installed.');
        }

        if (!class_exists('ErrorException')) {
            $this->markTestSkipped('Class ErrorException does not exists');
        }

        $client = new MockDeprecationsIoClient();
        $handler = new MonologV1Handler('https://ingest.deprecations.io/example?apikey=test', $client);

        $logger = new Logger('app', array($handler));
        $logger->notice('message.', array(
            'exception' => $this->createDeprecationException(),
        ));

        $this->assertSame(
            'https://ingest.deprecations.io/example?apikey=test',
            $client->events[0]['dsn']
        );

        $this->assertSame(
            'User Deprecated: deprecation example.',
            $client->events[0]['event']['deprecations'][0]['message']
        );
    }
}
