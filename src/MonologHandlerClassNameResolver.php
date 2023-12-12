<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeprecationsIo\Monolog;

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
        // Composer v2
        if (class_exists('Composer\InstalledVersions')) {
            try {
                $version = \Composer\InstalledVersions::getVersion('monolog/monolog');

                return sprintf('DeprecationsIo\Monolog\Handler\MonologV%sHandler', $version[0]);
            } catch (\OutOfBoundsException $e) {
                // Monolog is not installed
                return null;
            }
        }

        // Composer v1
        $reflection = new \ReflectionClass('Composer\Autoload\ClassLoader');

        $composerLockPath = dirname(dirname(dirname($reflection->getFileName()))) . '/composer.lock';
        if (!file_exists($composerLockPath)) {
            // Monolog is not installed
            return null;
        }

        $composerLock = json_decode(file_get_contents($composerLockPath), true);

        foreach ($composerLock['packages'] as $package) {
            if ($package['name'] === 'monolog/monolog') {
                return sprintf('DeprecationsIo\Monolog\Handler\MonologV%sHandler', $package['version'][0]);
            }
        }

        // Monolog is not installed
        return null;
    }
}
