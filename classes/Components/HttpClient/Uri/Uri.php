<?php

declare(strict_types=1);

namespace Xentral\Components\HttpClient\Uri;

use GuzzleHttp\Psr7\Uri as GuzzleUri;

final class Uri extends GuzzleUri implements UriInterface
{
    /**
     * @param GuzzleUri|string $uri
     *
     * @return Uri|UriInterface
     */
    public static function fromGuzzleUri($uri)
    {
        if (!$uri instanceof UriInterface) {
            return new self((string)$uri);
        }

        return $uri;
    }
}
