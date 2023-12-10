<?php

/*
 * This file is part of the deprecations.io project.
 *
 * (c) Titouan Galopin <titouan@deprecations.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeprecationsIo\Monolog\Context;

class Event
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $projectDir
     * @param array  $payload
     */
    public function __construct($projectDir, $payload)
    {
        $this->projectDir = $projectDir;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return $this->projectDir;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->payload;
    }

    /**
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return void
     */
    public function addDeprecation($message, $file, $line, array $trace)
    {
    }

    /**
     * @return string
     */
    private function normalizeTrace(array $trace)
    {
        $normalized = [];

        foreach ($trace as $item) {
            $reference = !empty($item['function']) ? $item['function'] : '';
            if (!empty($item['class']) && !empty($item['function'])) {
                $reference = $item['class'].'::'.$item['function'];
            }

            $normalized[] = implode("\t", [
                $this->normalizePath(!empty($item['file']) ? $item['file'] : ''),
                !empty($item['line']) ? $item['line'] : '',
                $reference,
            ]);
        }

        return implode("\n", $normalized);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizePath($path)
    {
        if (0 === strpos($path, $this->projectDir)) {
            return ltrim(substr($path, strlen($this->projectDir)), '/\\');
        }

        return ltrim($path, '/\\');
    }
}
