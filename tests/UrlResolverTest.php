<?php

namespace Tests;

use MotoGezi\GmapUrlResolver\Exceptions\UrlResolverException;
use MotoGezi\GmapUrlResolver\UrlResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class UrlResolverTest extends TestCase
{
    #[Test]
    #[DataProvider('sampleUrls')]
    public function check_sample_addresses($url)
    {
        $routeFinder = new UrlResolver();
        $fullUrl = $routeFinder->getFullUrl($url);

        $this->assertMatchesRegularExpression('#https://(?:www\.)?google.com/maps#', $fullUrl);
    }

    #[Test]
    public function handle_non_google_shortener_urls()
    {
        $this->expectException(UrlResolverException::class);
        $this->expectExceptionMessage('Unsupported URL: https://www.google.com');

        $url = 'https://www.google.com';
        $routeFinder = new UrlResolver();
        $routeFinder->getFullUrl($url);
    }

    #[Test]
    public function handle_non_direction_addresses()
    {
        $this->expectException(UrlResolverException::class);
        $this->expectExceptionMessage('This is not a direction URL, but a place:');

        $url = 'https://goo.gl/maps/aVradyonuMX75jKJ7'; // https://www.google.com/maps/place/FMV+Isik+University/@41.1688995,29.5618085,17z/data=!3m1!4b1!4m5!3m4!1s0x409e32a67119d599:0xf4e228611d2df2f0!8m2!3d41.1688995!4d29.5639972
        $routeFinder = new UrlResolver();
        $routeFinder->getFullUrl($url);
    }

    public static function sampleUrls()
    {
        return [
            ['https://goo.gl/maps/q8KoCq3j2Hz4DWBu8'], // Coords + data
            ['https://goo.gl/maps/UG6LhxzJBgFHzhrFA'], // Coords only
            ['https://goo.gl/maps/jY9XRRZM3S2Er9pR8'], // Multiple location names + data
            ['https://maps.app.goo.gl/v71vrnB8MWb4MTzc9'], // New URL format
            ['https://bit.ly/35q0vg7'], // Bit.ly
//            ['https://tinyurl.com/3x9z5z3u'], // TinyURL
//            ['https://is.gd/2Z7Z2'], // is.gd
//            ['https://cutt.ly/2Z7Z2'], // cutt.ly
//            ['https://rebrand.ly/2Z7Z2'], // rebrand.ly
//            ['https://buff.ly/2Z7Z2'], // buff.ly
//            ['https://t.co/2Z7Z2'], // t.co
//            ['https://lnkd.in/2Z7Z2'], // lnkd.in
//            ['https://ow.ly/2Z7Z2'], // ow.ly
//            ['https://ift.tt/2Z7Z2'], // ift.tt
//            ['https://dlvr.it/2Z7Z2'], // dlvr.it
//            ['https://wp.me/2Z7Z2'], // wp.me
//            ['https://amzn.to/2Z7Z2'], // amzn.to
//            ['https://fb.me/2Z7Z2'], // fb.me
        ];
    }
}
