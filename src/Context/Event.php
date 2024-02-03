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
     * @var ?string
     */
    private $command;

    /**
     * @var ?string
     */
    private $httpMethod;

    /**
     * @var ?string
     */
    private $httpUrl;

    /**
     * @var array
     */
    private $packages;

    /**
     * @var array
     */
    private $deprecations;

    /**
     * @param string $projectDir
     * @param ?string $command
     * @param ?string $httpMethod
     * @param ?string $httpUrl
     * @param array $packages
     */
    public function __construct($projectDir, $command, $httpMethod, $httpUrl, array $packages)
    {
        $this->projectDir = $projectDir;
        $this->command = $command;
        $this->httpMethod = $httpMethod;
        $this->httpUrl = $httpUrl;
        $this->packages = $packages;
        $this->deprecations = array();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $payload = array(
            'packages' => $this->packages,
            'deprecations' => $this->deprecations,
        );

        if ($this->command) {
            $payload['command'] = $this->command;
        } else {
            $payload['method'] = $this->httpMethod;
            $payload['url'] = $this->httpUrl;
        }

        return $payload;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return $this->projectDir;
    }

    /**
     * @return bool
     */
    public function hasDeprecations()
    {
        return !empty($this->deprecations);
    }

    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $trace
     * @param array $context
     * @return void
     */
    public function addDeprecation($message, $file, $line, $trace, $context)
    {
        $this->deprecations[] = array(
            'message' => $message,
            'file' => $file ? $this->normalizePath($file) : null,
            'line' => $line,
            'trace' => $trace ? $this->normalizeTrace($trace) : array(),
            'context' => $context ?: array(),
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
            $path = substr($path, strlen($this->projectDir));
        }

        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
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
