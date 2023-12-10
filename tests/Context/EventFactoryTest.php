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
    public function testCreateEvent()
    {
        $factory = new EventFactory();

        // Fake CLI command
        $_SERVER['argv'] = ['bin/console', 'cache:clear'];

        $this->assertInstanceOf('DeprecationsIo\Monolog\Context\Event', $factory->createEvent('cli'));
    }
}
