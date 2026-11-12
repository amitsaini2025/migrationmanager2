<?php

namespace App\Support;

/**
 * Adds query parameters to a stored notification URL without breaking
 * links that already have a query string (for example broadcast ?batch=).
 */
class NotificationUrl
{
    /**
     * @param  array<string, scalar|null>  $params
     */
    public static function withQuery(?string $url, array $params): string
    {
        $url = ($url === null || $url === '') ? url('/all-notifications') : $url;
        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }

        $query = [];
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $query[$key] = $value;
        }

        $built = '';
        if (isset($parts['scheme'])) {
            $built .= $parts['scheme'].'://';
            if (isset($parts['user'])) {
                $built .= $parts['user'];
                if (isset($parts['pass'])) {
                    $built .= ':'.$parts['pass'];
                }
                $built .= '@';
            }
            $built .= $parts['host'] ?? '';
            if (isset($parts['port'])) {
                $built .= ':'.$parts['port'];
            }
        }

        $built .= $parts['path'] ?? '';
        if ($query !== []) {
            $built .= '?'.http_build_query($query);
        }
        if (! empty($parts['fragment'])) {
            $built .= '#'.$parts['fragment'];
        }

        return $built;
    }
}
