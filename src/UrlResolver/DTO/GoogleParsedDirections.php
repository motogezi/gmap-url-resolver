<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\DTO;

class GoogleParsedDirections
{
    public ?string $origin = null;
    public ?string $destination = null;
    public array $waypoints = [];
    public bool $avoidTolls = false;
    public bool $avoidHighways = false;
    public bool $avoidFerries = false;

    /**
     * a.k.a centroid. @45.5332554,-122.681977,15z -> 15z is the zoom level. 1~20.
     * Bazen birden fazla olabiliyor.
     */
    public ?string $centerCoords = null;
}
