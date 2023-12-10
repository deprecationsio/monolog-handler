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

class EventFactory
{
    private $cache;

    /**
     * @param string $sapi
     *
     * @return Event
     */
    public function createEvent($sapi)
    {
        if (!$this->cache) {
            $this->cache = $this->createContextCache($sapi);
        }

        return new Event($this->cache['projectDir'], $this->cache['context']);
    }

    /**
     * @param string $sapi
     *
     * @return array
     */
    private function createContextCache($sapi)
    {
        // Project dir
        $composerJson = dirname(dirname(dirname(dirname(__DIR__)))).'/composer.json';
        if (!file_exists($composerJson)) {
            $composerJson = dirname(dirname(__DIR__)).'/composer.json';
        }

        // Context
        $context = [];
        if ('cli' === $sapi) {
            $context['command'] = implode(' ', !empty($_SERVER['argv']) ? $_SERVER['argv'] : []);
        } else {
            $context['method'] = strtoupper(!empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
            $context['url'] = $this->getRequestUrl();
        }

        return [
            'projectDir' => file_exists($composerJson) ? dirname($composerJson) : null,
            'context' => $context,
        ];
    }

    /**
     * @return string
     */
    private function getRequestUrl()
    {
        // IIS rewritten URL
        if (!empty($_SERVER['IIS_WasUrlRewritten'])
            && 1 === ((int) $_SERVER['IIS_WasUrlRewritten'])
            && !empty($_SERVER['UNENCODED_URL'])) {
            return (string) $_SERVER['UNENCODED_URL'];
        }

        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];

            if ('' !== $requestUri && '/' === $requestUri[0]) {
                // To only use path and query remove the fragment.
                if (false !== $pos = strpos($requestUri, '#')) {
                    $requestUri = substr($requestUri, 0, $pos);
                }
            } else {
                // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path,
                // only use URL path.
                $uriComponents = parse_url($requestUri);

                if (isset($uriComponents['path'])) {
                    $requestUri = $uriComponents['path'];
                }

                if (isset($uriComponents['query'])) {
                    $requestUri .= '?'.$uriComponents['query'];
                }
            }

            return (string) $requestUri;
        }

        // IIS 5.0, PHP as CGI
        if (!empty($_SERVER['ORIG_PATH_INFO'])) {
            $requestUri = $_SERVER['ORIG_PATH_INFO'];

            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?'.$_SERVER['QUERY_STRING'];
            }

            return (string) $requestUri;
        }

        return '';
    }
}
