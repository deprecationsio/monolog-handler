<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Deprecationsio\Monolog\Context;

use Deprecationsio\Monolog\Context\EventFactory;
use Tests\Deprecationsio\Monolog\UnitTest;

class EventFactoryTest extends UnitTest
{
    public function provideCreateEvent()
    {
        $packages = array(
            'deprecationsio/monolog-handler' => 'dev-main',
            'doctrine/instantiator' => '2.0.0.0',
            'monolog/monolog' => '3.5.0.0',
            'myclabs/deep-copy' => '1.11.1.0',
            'nikic/php-parser' => '5.0.0.0',
            'phar-io/manifest' => '2.0.3.0',
            'phar-io/version' => '3.2.1.0',
            'phpunit/php-code-coverage' => '9.2.30.0',
            'phpunit/php-file-iterator' => '3.0.6.0',
            'phpunit/php-invoker' => '3.1.1.0',
            'phpunit/php-text-template' => '2.0.4.0',
            'phpunit/php-timer' => '5.0.3.0',
            'phpunit/phpunit' => '9.6.16.0',
            'psr/log' => '3.0.0.0',
            'psr/log-implementation' => 'dev-main',
            'sebastian/cli-parser' => '1.0.1.0',
            'sebastian/code-unit' => '1.0.8.0',
            'sebastian/code-unit-reverse-lookup' => '2.0.3.0',
            'sebastian/comparator' => '4.0.8.0',
            'sebastian/complexity' => '2.0.3.0',
            'sebastian/diff' => '4.0.5.0',
            'sebastian/environment' => '5.1.5.0',
            'sebastian/exporter' => '4.0.5.0',
            'sebastian/global-state' => '5.0.6.0',
            'sebastian/lines-of-code' => '1.0.4.0',
            'sebastian/object-enumerator' => '4.0.4.0',
            'sebastian/object-reflector' => '2.0.4.0',
            'sebastian/recursion-context' => '4.0.5.0',
            'sebastian/resource-operations' => '3.0.3.0',
            'sebastian/type' => '3.2.1.0',
            'sebastian/version' => '3.0.2.0',
            'theseer/tokenizer' => '1.2.2.0',
        );

        return array(
            array(
                'sapi' => 'cli',
                'server' => array(
                    'argv' => array('bin/console', 'cache:clear'),
                ),
                'expectedProjectDir' => dirname(dirname(__DIR__)),
                'expectedPayload' => array(
                    'packages' => $packages,
                    'deprecations' => array(),
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
                    'packages' => $packages,
                    'deprecations' => array(),
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
                    'packages' => $packages,
                    'deprecations' => array(),
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

        $this->assertInstanceOf('Deprecationsio\Monolog\Context\Event', $event);
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
        $this->assertInstanceOf('Deprecationsio\Monolog\Context\Event', $event);

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
        $this->assertSame('tests/Context/EventFactoryTest.php	125	Tests\\Deprecationsio\\Monolog\\UnitTest::createDeprecationException', $traceLines[0]);
    }
}
