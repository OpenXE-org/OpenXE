<?php

namespace Xentral\Components\Sanitizer\Helper;

use Xentral\Components\Sanitizer\Exception\InvalidArgumentException;
use Xentral\Components\Sanitizer\Exception\InvalidUrlException;

final class UriParser
{
    /**
     * @param string $url
     *
     * @throws InvalidArgumentException
     * @throws InvalidUrlException
     *
     * @return UriDefinition
     */
    public function parse($url)
    {
        if (!is_string($url) || empty($url)) {
            throw new InvalidArgumentException('Url is invalid. Url can not be empty.');
        }

        $parts = @parse_url($url);
        if ($parts === false) {
            throw new InvalidUrlException(sprintf('Could not parse url: "%s"', $url));
        }

        $queryParams = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParams);
        }

        return new UriDefinition(
            isset($parts['scheme']) ? $parts['scheme'] : null,
            isset($parts['user']) ? $parts['user'] : null,
            isset($parts['pass']) ? $parts['pass'] : null,
            isset($parts['host']) ? $parts['host'] : null,
            isset($parts['port']) ? (int)$parts['port'] : null,
            isset($parts['path']) ? $parts['path'] : null,
            is_array($queryParams) ? $queryParams : null,
            isset($parts['fragment']) ? $parts['fragment'] : null
        );
    }
}
