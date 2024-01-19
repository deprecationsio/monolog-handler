<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deprecationsio\Monolog;

use Deprecationsio\Monolog\Composer\ComposerDataReader;

/**
 * Resolve the class name of the Monolog handler to use depending on the Monolog version.
 */
class MonologHandlerClassNameResolver
{
    /**
     * @return string|null
     */
    public static function resolveHandlerClassName()
    {
        $installedPackages = ComposerDataReader::getInstalledPackages();

        // Monolog is not installed
        if (!isset($installedPackages['monolog/monolog'])) {
            return null;
        }

        return sprintf('Deprecationsio\Monolog\Handler\MonologV%sHandler', $installedPackages['monolog/monolog'][0]);
    }
}
