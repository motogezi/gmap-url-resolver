<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\Extractors;

use MotoGezi\GmapUrlResolver\UrlResolver;
use Illuminate\Support\Facades\Http;

class Bitly implements ShortUrlExtractor
{
    public function resolve(string $url): ?string
    {
        if (! preg_match('#^https://bit\.ly/[a-zA-Z\d]+$#', $url)) {
            return null;
        }

        $redirectedUrl = Http::withoutRedirecting()
            ->timeout(5)
            ->head($url)
            ->header('location');

        if (! $redirectedUrl) {
            return null;
        }

        return app(UrlResolver::class)->getFullUrl($redirectedUrl);
    }
}
