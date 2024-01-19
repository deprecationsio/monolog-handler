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

use Deprecationsio\Monolog\Context\DeprecationFactory;
use Deprecationsio\Monolog\Context\Event;
use Tests\Deprecationsio\Monolog\UnitTest;

class DeprecationFactoryTest extends UnitTest
{
    public function provideAddDeprecationToEvent()
    {
        return array(
            array(
                'message' => 'The "Tests\Deprecationsio\Monolog\Mock\MockClass" class implements "Example" that is deprecated since Symfony 6.2, use the {@see AsMessageHandler} attribute instead.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Tests\Deprecationsio\Monolog\Mock\MockClass" interface extends "Example" that is deprecated since Symfony 6.2.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Example" class is considered final. It may change without further notice as of its next major version. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Example" interface is considered internal. It may change without further notice. You should not use it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Example::foo" method is considered internal. It may change without further notice. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'Class "Tests\Deprecationsio\Monolog\Mock\MockClass" should implement method "Example::foo".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Example" class is considered final. It may change without further notice as of its next major version. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
            array(
                'message' => 'The "Invalid\MockClass" class implements "Symfony\Component\Messenger\Handler\MessageHandlerInterface" that is deprecated since Symfony 6.2, use the {@see AsMessageHandler} attribute instead.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'expectedLine' => 10,
            ),
            array(
                'message' => 'User Deprecated: The "Tests\Deprecationsio\Monolog\Mock\MockClass" class implements "Doctrine\DBAL\Driver\ServerInfoAwareConnection" that is deprecated The methods defined in this interface will be made part of the {@see Driver} interface in the next major release.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
            ),
        );
    }

    /**
     * @dataProvider provideAddDeprecationToEvent
     */
    public function testAddDeprecationToEvent($message, $file, $line, $expectedFile, $expectedLine)
    {
        $event = new Event(dirname(dirname(__DIR__)), 'bin/phpunit', null, null, array());

        $factory = new DeprecationFactory();
        $factory->addDeprecationToEvent($event, $message, $file, $line, array());

        $data = $event->toArray();
        $this->assertSame($expectedFile, $data['deprecations'][0]['file']);
        $this->assertSame($expectedLine, $data['deprecations'][0]['line']);
    }
}
