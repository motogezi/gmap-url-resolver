<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\Extractors;

class FullGoogleMapsUrl implements ShortUrlExtractor
{
    public function resolve(string $url): ?string
    {
        if (preg_match('#^https://(www\.)?google\.com/maps/dir/.+#', $url)) {
            return $url;
        }

         return null;
    }
}
