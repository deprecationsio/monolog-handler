<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deprecationsio\Monolog\Composer;

use Composer\InstalledVersions;

class ComposerDataReader
{
    private static $composerVersion;
    private static $projectDir;
    private static $installedPackages;
    private static $composerLock;

    public static function getInstalledPackages()
    {
        if (self::$installedPackages) {
            return self::$installedPackages;
        }

        if (self::getComposerVersion() === 1) {
            // Composer 1
            $composerLock = self::getComposerLock();

            $packages = array();
            foreach ($composerLock['packages'] as $package) {
                $packages[$package['name']] = $package['version'];
            }

            return self::$installedPackages = $packages;
        }

        // Composer 2
        $rawData = InstalledVersions::getAllRawData();
        $packages = array();
        foreach ($rawData[0]['versions'] as $packageName => $details) {
            $packages[$packageName] = !empty($details['version']) ? $details['version'] : 'dev-main';
        }

        return self::$installedPackages = $packages;
    }

    public static function getProjectDir()
    {
        if (self::$projectDir) {
            return self::$projectDir;
        }

        if (self::getComposerVersion() === 1) {
            // Composer 1
            $reflection = new \ReflectionClass('Composer\Autoload\ClassLoader');

            return self::$projectDir = dirname(dirname(dirname($reflection->getFileName())));
        }

        // Composer 2
        $rootPackage = InstalledVersions::getRootPackage();

        return self::$projectDir = realpath($rootPackage['install_path']);
    }

    private static function getComposerLock()
    {
        if (self::$composerLock) {
            return self::$composerLock;
        }

        $composerLockPath = self::getProjectDir().'/composer.lock';
        if (!file_exists($composerLockPath)) {
            return null;
        }

        return self::$composerLock = json_decode(file_get_contents($composerLockPath), true);
    }

    private static function getComposerVersion()
    {
        if (!self::$composerVersion) {
            self::$composerVersion = class_exists('Composer\InstalledVersions') ? 2 : 1;
        }

        return self::$composerVersion;
    }
}