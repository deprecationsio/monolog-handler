<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deprecationsio\Monolog\Context;

class DeprecationFactory
{
    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $trace
     * @return void
     */
    public function addDeprecationToEvent(Event $event, $message, $file, $line, $trace)
    {
        // Handle Symfony DebugClassLoader deprecations
        if ($file
            && false !== strpos($file, 'symfony')
            && false !== strpos($file, 'DebugClassLoader.php')
            && (
                preg_match('/.*The "(.+)" (?:class|interface|trait|method) (?:implements|uses|extends).+that is deprecated.*/U', $message, $m)
                || preg_match('/.*The ".+" (?:class|interface|trait|method).+You should not (?:extend|use|implement) it from "(.+)".*/U', $message, $m)
                || preg_match('/.*(?:Class|Interface|Trait) "(.+)" should implement method.*/U', $message, $m)
            )
        ) {
            if (class_exists($m[1])) {
                $reflection = new \ReflectionClass($m[1]);
                $file = $reflection->getFileName() ? (string) $reflection->getFileName() : $file;
                $line = $reflection->getStartLine() ? (int) $reflection->getStartLine() : $line;
            }
        }

        $event->addDeprecation($message, $file, $line, $trace);
    }
}
