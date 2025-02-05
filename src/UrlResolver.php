<?php

namespace MotoGezi\GmapUrlResolver;

use MotoGezi\GmapUrlResolver\Exceptions\UrlResolverException;
use MotoGezi\GmapUrlResolver\UrlResolver\Extractors\Bitly;
use MotoGezi\GmapUrlResolver\UrlResolver\Extractors\FullGoogleMapsUrl;
use MotoGezi\GmapUrlResolver\UrlResolver\Extractors\Googl;
use MotoGezi\GmapUrlResolver\UrlResolver\Extractors\ShortUrlExtractor;

class UrlResolver
{
    /**
     * @var ShortUrlExtractor[]
     */
    private const EXTRACTORS = [
        FullGoogleMapsUrl::class,
        Googl::class,
        Bitly::class,
    ];

    public function getFullUrl($url): ?string
    {
        $fullUrl = null;
        foreach(self::EXTRACTORS as $extractor) {
            $fullUrl = (new $extractor())->resolve($url);
            if ($fullUrl) {
                break;
            }
        }

        if (! $fullUrl) {
            throw new UrlResolverException('Unsupported URL: ' . $url);
        }

        return $fullUrl;
    }
}
