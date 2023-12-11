<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Handler;

use DeprecationsIo\Monolog\Handler\MonologV3Handler;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\DeprecationsIo\Monolog\Client\MockDeprecationsIoClient;
use Tests\DeprecationsIo\Monolog\UnitTest;

class MonologV3HandlerTest extends UnitTest
{
    public function testHandle()
    {
        if (3 !== $this->getMonologVersion()) {
            $this->markTestSkipped('Monolog v3 not installed.');
        }

        $client = new MockDeprecationsIoClient();

        $handler = new MonologV3Handler($client, 'https://ingest.deprecations.io/example?apikey=test');

        $this->assertTrue($handler->isHandling(new LogRecord(
            new \DateTimeImmutable(),
            'app',
            Level::Notice,
            'message',
            array(
                'exception' => $this->createDeprecationException(),
            )
        )));

        $handler->handle(new LogRecord(
            new \DateTimeImmutable(),
            'app',
            Level::Notice,
            'message',
            array(
                'exception' => $this->createDeprecationException(),
            )
        ));

        $this->assertSame(
            'https://ingest.deprecations.io/example?apikey=test',
            $client->events[0]['dsn']
        );

        $this->assertSame(
            'User Deprecated: Method \\"Symfony\\Component\\HttpKernel\\Bundle\\Bundle::build()\\" might add \\"void\\" as a native return type declaration in the future.',
            $client->events[0]['event']['deprecations'][0]['message']
        );
    }
}
