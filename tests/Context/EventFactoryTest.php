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
use PHPUnit\Framework\TestCase;

class EventFactoryTest extends TestCase
{
    public function testCreateEvent()
    {
        $factory = new EventFactory();

        // Fake CLI command
        $_SERVER['argv'] = array('bin/console', 'cache:clear');

        dump($factory->createEvent('cli'));exit;

        $this->assertTrue(true);
    }
}
