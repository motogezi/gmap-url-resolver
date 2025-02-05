<?php

namespace MotoGezi\GmapUrlResolver\UrlResolver\Extractors;

use MotoGezi\GmapUrlResolver\UrlResolver\DTO\GoogleParsedDirections;
use dobron\GoogleMapsQueryArgs;
use Illuminate\Support\Facades\Log;

class ParseDirectionsUrl
{
    public function handle(string $url): GoogleParsedDirections
    {
        $parsedUrl = parse_url($url);
        $urlPath = preg_replace('#^/maps/dir/#', '', $parsedUrl['path']);

        $parts = explode('/', $urlPath);
        $parts = array_map('urldecode', $parts);

        $directions = new GoogleParsedDirections();

        // See https://developers.google.com/maps/documentation/urls/get-started
        // If the part starts with:
        //   @    -> center of the map
        //   data -> additional info. Seçili route da burada saklanır.
        //   am=t -> ?
        $data = null;
        $waypoints = [];
        foreach ($parts as $part) {
            if (str_starts_with($part, '@')) {
                $directions->centerCoords = $part;
                continue;
            }

            if (str_starts_with($part, 'data=')) {
                $data = str_replace('data=', '', $part);
                continue;
            }

            // E.g. am=t -> shows directions pane
            if (str_starts_with($part, 'am=')) {
                continue;
            }

            $waypoints[] = $part;
        }

        if (! empty($data)) {
            $this->parseProtobuf($data, $directions);
        } else {
            Log::info('$data empty', compact('url'));
        }

        // https://developers.google.com/maps/documentation/directions/get-directions#maps_http_directions_toronto_montreal-txt
        $directions->origin = array_shift($waypoints);
        $directions->destination = array_pop($waypoints);

        //        dd($data);
//        $data = array_filter($data, fn ($value) => str_starts_with($value, 'd'));
//        $data = array_values($data);
//        $grouped = [];
//        for ($i = 0; $i < count($data); $i += 2) {
//            $val1 = str_replace('d', '', $data[$i]);
//            $val2 = str_replace('d', '', $data[$i + 1] ?? '');
//            $grouped[] = 'via:' . $val2 . ',' . $val1;
//        }
//        $this->waypoints = $grouped;

        // TODO: Data'da !2m1!2b1 varsa -> avoid tolls
        $directions->waypoints = $waypoints;

        return $directions;
    }

    private function parseProtobuf(string $data, GoogleParsedDirections $directions): void
    {
        $data = GoogleMapsQueryArgs::decode($data);

        if (($data[4][4][2][1] ?? null) == 'b1') {
            $directions->avoidHighways = true;
        }
        if (($data[4][4][2][2] ?? null) == 'b1') {
            $directions->avoidTolls = true;
        }
        if (($data[4][4][2][3] ?? null) == 'b1') {
            $directions->avoidFerries = true;
        }
    }
}
