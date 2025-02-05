<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\Extractors;

interface ShortUrlExtractor
{
    public function resolve(string $url): ?string;
}
