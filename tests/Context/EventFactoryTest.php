<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\DeprecationsIo\Monolog\Context;

use DeprecationsIo\Monolog\Context\EventFactory;
use Tests\DeprecationsIo\Monolog\UnitTest;

class EventFactoryTest extends UnitTest
{
    public function provideCreateEvent()
    {
        return array(
            array(
                'sapi' => 'cli',
                'server' => array(
                    'argv' => array('bin/console', 'cache:clear'),
                ),
                'expectedProjectDir' => dirname(dirname(__DIR__)),
                'expectedPayload' => array(
                    'command' => 'bin/console cache:clear',
                ),
            ),
            array(
                'sapi' => 'fpm',
                'server' => array(
                    'REQUEST_URI' => 'https://example.com/path/to/url?query=param#hash',
                ),
                'expectedProjectDir' => dirname(dirname(__DIR__)),
                'expectedPayload' => array(
                    'method' => 'GET',
                    'url' => '/path/to/url?query=param',
                ),
            ),
            array(
                'sapi' => 'fpm',
                'server' => array(
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => '/path/to/url?query=param#hash',
                ),
                'expectedProjectDir' => dirname(dirname(__DIR__)),
                'expectedPayload' => array(
                    'method' => 'POST',
                    'url' => '/path/to/url?query=param',
                ),
            ),
        );
    }

    /**
     * @dataProvider provideCreateEvent
     */
    public function testCreateEvent($sapi, $server, $expectedProjectDir, $expectedPayload)
    {
        $_SERVER = array_merge($_SERVER, $server);

        $factory = new EventFactory();
        $event = $factory->createEvent($sapi);

        $this->assertInstanceOf('DeprecationsIo\Monolog\Context\Event', $event);
        $this->assertSame($expectedProjectDir, $event->getProjectDir());
        $this->assertSame($expectedPayload, $event->toArray());
    }

    public function testAddDeprecation()
    {
        $_SERVER = array_merge($_SERVER, array(
            'argv' => array('bin/console', 'cache:clear'),
        ));

        $factory = new EventFactory();

        $event = $factory->createEvent('cli');
        $this->assertInstanceOf('DeprecationsIo\Monolog\Context\Event', $event);

        $exception = $this->createDeprecationException();
        $event->addDeprecation(
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTrace()
        );

        $details = $event->toArray();
        $this->assertSame('bin/console cache:clear', $details['command']);
        $this->assertSame(
            'User Deprecated: deprecation example.',
            $details['deprecations'][0]['message']
        );
        $this->assertSame('tests/UnitTest.php', $details['deprecations'][0]['file']);
        $this->assertSame(27, $details['deprecations'][0]['line']);

        $traceLines = explode("\n", $details['deprecations'][0]['trace']);
        $this->assertSame('tests/Context/EventFactoryTest.php	84	Tests\\DeprecationsIo\\Monolog\\UnitTest::createDeprecationException', $traceLines[0]);
    }
}
