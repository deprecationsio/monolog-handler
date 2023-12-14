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
     * @param array $payload
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
     * @return bool
     */
    public function hasDeprecations()
    {
        return !empty($this->payload['deprecations']);
    }

    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $trace
     * @return void
     */
    public function addDeprecation($message, $file, $line, $trace)
    {
        $this->payload['deprecations'][] = array(
            'message' => $message,
            'file' => $file ? $this->normalizePath($file) : null,
            'line' => $line,
            'trace' => $trace ? $this->normalizeTrace($trace) : array(),
        );
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

    /**
     * @return string
     */
    private function normalizeTrace(array $trace)
    {
        $normalized = array();

        foreach ($trace as $item) {
            $reference = !empty($item['function']) ? $item['function'] : '';
            if (!empty($item['class']) && !empty($item['function'])) {
                $reference = $item['class'] . '::' . $item['function'];
            }

            $normalized[] = implode("\t", array(
                $this->normalizePath(!empty($item['file']) ? $item['file'] : ''),
                !empty($item['line']) ? $item['line'] : '',
                $reference,
            ));
        }

        return implode("\n", $normalized);
    }
}
