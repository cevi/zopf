<?php

namespace Tests\Feature;

use App\Helper\Helper;
use App\Support\FakeGeocoder;
use Spatie\Geocoder\Facades\Geocoder as GeocoderFacade;
use Spatie\Geocoder\Geocoder;
use Tests\TestCase;

/**
 * Geocoding (Google) ist in Tests durch FakeGeocoder ersetzt (GEOCODER_FAKE in phpunit.xml und .env.dusk.local):
 * Bestellungen, Import und Aktionen sind ohne Netzwerk testbar.
 */
class GeocoderTest extends TestCase
{
    public function test_all_entry_points_use_the_fake(): void
    {
        $this->assertInstanceOf(FakeGeocoder::class, app(Geocoder::class));
        $this->assertInstanceOf(FakeGeocoder::class, GeocoderFacade::getFacadeRoot());
        $this->assertInstanceOf(FakeGeocoder::class, Helper::getGeocoder('test-key'));
    }

    public function test_fake_returns_coordinates_in_switzerland(): void
    {
        $result = Helper::getGeocoder('test-key')->getCoordinatesForAddress('Bahnhofstrasse 1, 8001 Zürich');

        $this->assertEqualsWithDelta(47.4, $result['lat'], 1.0);
        $this->assertEqualsWithDelta(8.5, $result['lng'], 1.0);
        // gleiche Adresse, gleiche Koordinaten
        $this->assertSame($result, Helper::getGeocoder('test-key')->getCoordinatesForAddress('Bahnhofstrasse 1, 8001 Zürich'));
    }
}
