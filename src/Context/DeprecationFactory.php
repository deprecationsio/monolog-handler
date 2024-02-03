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
            list($file, $line) = $this->resolveRealTriggeringFile($m[1], $file, $line);
        }

        // Handle Symfony services definitions
        if (preg_match('/.*The "(?:.+)" (?:service|service alias) is deprecated.*It is being referenced by the "(.+)" service./U', $message, $m)) {
            list($file, $line) = $this->resolveRealTriggeringFile($m[1], $file, $line);
        }

        // Add context
        $context = array();
        if ($file && is_file($file)) {
            $code = new \SplFileObject($file);
            for ($i = $line - 3; $i <= $line + 3; ++$i) {
                if ($line < 0) {
                    continue;
                }

                $code->seek($i);
                $context[$i + 1] = trim($code->current());
            }
        }

        $event->addDeprecation($message, $file, $line, $trace, array_filter($context));
    }

    /**
     * @param string $className
     * @return array
     */
    private function resolveRealTriggeringFile($className, $file, $line)
    {
        $parts = explode('::', $className);
        $className = $parts[0];

        if (!class_exists($className)) {
            return array($file, $line);
        }

        $reflection = new \ReflectionClass($className);

        return array(
            $reflection->getFileName() ? (string) $reflection->getFileName() : $file,
            $reflection->getStartLine() ? (int) $reflection->getStartLine() : $line
        );
    }
}
