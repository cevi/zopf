<?php

namespace Tests;

use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Stammdaten (Status, Rollen, Admin, Global-Gruppe/-Aktion) ohne Demo-Daten und ohne Ortschaften-Import.
     */
    protected $seeder = TestingSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        // Tests brauchen kein gebautes Frontend (public/build).
        $this->withoutVite();

        // Keine echten HTTP-Aufrufe über den Http-Client (Geocoder nutzt Guzzle direkt, siehe Helper::getGeocoder).
        Http::preventStrayRequests();

        // Excel-Downloads (maatwebsite/excel) löschen ihre Temp-Datei erst nach dem Senden, was im Test nie passiert.
        config(['excel.temporary_files.local_path' => sys_get_temp_dir().'/laravel-excel-tests']);
    }

    /**
     * RefreshDatabase leert die Datenbank. Bei gecachter Config (php artisan config:cache)
     * greifen die Werte aus phpunit.xml nicht und es würde die echte Datenbank treffen.
     */
    protected function beforeRefreshingDatabase()
    {
        $connection = config('database.default');
        $database = config("database.connections.$connection.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                "Tests würden gegen '$connection' ($database) laufen statt gegen SQLite in-memory. "
                ."Config-Cache aktiv? 'php artisan config:clear' ausführen."
            );
        }
    }
}
