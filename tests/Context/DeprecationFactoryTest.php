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
                'message' => 'Example.',
                'file' => 'vendor/symfony/any/file.php',
                'line' => 10,
                'expectedFile' => 'vendor/symfony/any/file.php',
                'expectedLine' => 10,
                'epxectedContext' => array(),
            ),
            array(
                'message' => 'The "Tests\Deprecationsio\Monolog\Mock\MockClass" class implements "Example" that is deprecated since Symfony 6.2, use the {@see AsMessageHandler} attribute instead.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Tests\Deprecationsio\Monolog\Mock\MockClass" interface extends "Example" that is deprecated since Symfony 6.2.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Example" class is considered final. It may change without further notice as of its next major version. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Example" interface is considered internal. It may change without further notice. You should not use it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Example::foo" method is considered internal. It may change without further notice. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass::bar".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'Class "Tests\Deprecationsio\Monolog\Mock\MockClass" should implement method "Example::foo".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Example" class is considered final. It may change without further notice as of its next major version. You should not extend it from "Tests\Deprecationsio\Monolog\Mock\MockClass".',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'The "Invalid\MockClass" class implements "Symfony\Component\Messenger\Handler\MessageHandlerInterface" that is deprecated since Symfony 6.2, use the {@see AsMessageHandler} attribute instead.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'expectedLine' => 10,
                'epxectedContext' => array(),
            ),
            array(
                'message' => 'User Deprecated: The "Tests\Deprecationsio\Monolog\Mock\MockClass" class implements "Doctrine\DBAL\Driver\ServerInfoAwareConnection" that is deprecated The methods defined in this interface will be made part of the {@see Driver} interface in the next major release.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'Since symfony/security-bundle 6.2: The "Symfony\Component\Security\Core\Security" service alias is deprecated, use "Symfony\Bundle\SecurityBundle\Security" instead. It is being referenced by the "Tests\Deprecationsio\Monolog\Mock\MockClass" service.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'tests/Mock/MockClass.php',
                'expectedLine' => 14,
                'epxectedContext' => array(
                    12 => 'namespace Tests\Deprecationsio\Monolog\Mock;',
                    14 => 'class MockClass',
                    15 => '{',
                    16 => '}',
                ),
            ),
            array(
                'message' => 'Since symfony/security-bundle 6.2: The "Symfony\Component\Security\Core\Security" service alias is deprecated, use "Symfony\Bundle\SecurityBundle\Security" instead. It is being referenced by the "app.example_service" service.',
                'file' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'line' => 10,
                'expectedFile' => 'vendor/symfony/error-handler/DebugClassLoader.php',
                'expectedLine' => 10,
                'epxectedContext' => array(),
            ),
        );
    }

    /**
     * @dataProvider provideAddDeprecationToEvent
     */
    public function testAddDeprecationToEvent($message, $file, $line, $expectedFile, $expectedLine, $expectedContext)
    {
        $event = new Event(dirname(dirname(__DIR__)), 'bin/phpunit', null, null, array());

        $factory = new DeprecationFactory();
        $factory->addDeprecationToEvent($event, $message, $file, $line, array());

        $data = $event->toArray();
        $this->assertSame($expectedFile, $data['deprecations'][0]['file']);
        $this->assertSame($expectedLine, $data['deprecations'][0]['line']);
        $this->assertSame(array(), $data['deprecations'][0]['trace']);
        $this->assertSame($expectedContext, $data['deprecations'][0]['context']);
    }
}
