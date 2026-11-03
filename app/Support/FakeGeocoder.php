<?php

namespace App\Support;

use GuzzleHttp\Client;
use Spatie\Geocoder\Geocoder;

/**
 * Geocoder für Tests und Dusk (GEOCODER_FAKE=true): kein Aufruf bei Google.
 * Liefert Koordinaten im Raum Zürich, für dieselbe Adresse immer dieselben.
 */
class FakeGeocoder extends Geocoder
{
    public function __construct()
    {
        parent::__construct(new Client);
    }

    public function getCoordinatesForAddress(string $address): array
    {
        $hash = crc32($address);

        return [
            'lat' => 47.37 + ($hash % 1000) / 100000,
            'lng' => 8.54 + (intdiv($hash, 1000) % 1000) / 100000,
            'accuracy' => 'ROOFTOP',
            'formatted_address' => $address,
            'viewport' => [],
        ];
    }

    public function getAllCoordinatesForAddress(string $address): array
    {
        return [$this->getCoordinatesForAddress($address)];
    }
}
