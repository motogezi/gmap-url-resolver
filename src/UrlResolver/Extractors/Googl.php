<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\Extractors;

use MotoGezi\GmapUrlResolver\Exceptions\UrlResolverException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Googl implements ShortUrlExtractor
{
    public function resolve(string $url): ?string
    {
        $oldUrl = preg_match('#^https://goo\.gl/maps/[a-zA-Z\d]+$#', $url);
        $newUrl = preg_match('#^https://maps\.app\.goo\.gl/[a-zA-Z\d]+$#', $url);
        if (! $oldUrl && ! $newUrl) {
            return null;
        }

        return $this->getFullUrlFromContent($url);
    }

    private function getFullUrlFromContent($url): string
    {
        $response = Http::get($url);

        // VPN ile bağlanınca başka, evden bağlanınca başka response dönüyor.
        // Evden
        if (preg_match('#\"(/dir/.+?)\\\\"#', $response->body(), $match)) {
            // Fix data=...
            $fullUrl = str_replace('\\\\u003d', '=', $match[1]);
            return 'https://google.com/maps' . $fullUrl;
        }

        if (preg_match('/place\/([^\/]+)\/@([^,]+),([^,]+)/', $response->body(), $match)) {
            // $placeName = $matches[1];
            // $latitude = $matches[2];
            // $longitude = $matches[3];
            throw new UrlResolverException('This is not a direction URL, but a place: ' . $url);
        }

        // Almanya'da cookie consent sayfası çıkıyor.
        if (preg_match('#value="(https://www.google.com/maps/dir/.+?)">#', $response->body(), $match)) {
            return $match[1];
        }

        Log::error('Cannot resolve URL', ['url' => $url, 'response' => $response->body()]);
        throw new UrlResolverException('Invalid full url. Directions not found.');
    }
}
