<?php

namespace Tests;

use Database\Seeders\TestingSeeder;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use RuntimeException;

/**
 * Basis der Browser-Tests (tests/Browser, sail dusk). Umgebung aus .env.dusk.local, Testdaten wie in den
 * Feature-Tests (TestingSeeder, ActionScenario), aber in einer SQLite-Datei, damit der Webserver sie sieht.
 */
abstract class DuskTestCase extends BaseTestCase
{
    // Einmal migrieren, vor jedem Test leeren und neu seeden. DatabaseMigrations würde nach jedem Test
    // migrate:rollback ausführen, das an nicht umkehrbaren down()-Methoden scheitert.
    use DatabaseTruncation;

    protected $seed = true;

    protected $seeder = TestingSeeder::class;

    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Läuft vor migrate:fresh und vor dem Leeren: nie die Entwicklungs- oder Produktionsdatenbank leeren.
     */
    protected function beforeTruncatingDatabase(): void
    {
        $database = (string) config('database.connections.sqlite.database');
        if (config('database.default') !== 'sqlite' || ! str_ends_with($database, 'dusk.sqlite')) {
            throw new RuntimeException('Dusk läuft nur mit database/dusk.sqlite (.env.dusk.local), nicht mit '.config('database.default').'.');
        }
        if (! file_exists($database)) {
            touch($database);
        }
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            // Service Worker (PWA) gibt es nur in sicheren Kontexten; die Testadresse ist http
            '--unsafely-treat-insecure-origin-as-secure='.rtrim(config('app.url'), '/'),
            // Keine echten Dienste aus dem Browser: Pusher (Key steckt im gebauten Bundle) und Google Maps
            '--host-resolver-rules='.implode(', ', [
                'MAP *.pusher.com 127.0.0.1',
                'MAP *.pusherapp.com 127.0.0.1',
                'MAP maps.googleapis.com 127.0.0.1',
                'MAP maps.gstatic.com 127.0.0.1',
            ]),
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge(['--disable-gpu', '--headless=new']);
        })->all());

        // Konsolenausgaben des Browsers sammeln, siehe consoleErrors()
        $capabilities = DesiredCapabilities::chrome()
            ->setCapability(ChromeOptions::CAPABILITY, $options)
            ->setCapability('goog:loggingPrefs', ['browser' => 'ALL']);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            $capabilities
        );
    }

    /**
     * Fehler in der Browser-Konsole seit dem letzten Aufruf (JS-Fehler, fehlgeschlagene Anfragen).
     *
     * @return list<string>
     */
    protected function consoleErrors(Browser $browser): array
    {
        return collect($browser->driver->manage()->getLog('browser'))
            ->where('level', 'SEVERE')
            ->pluck('message')
            ->values()
            ->all();
    }

    /**
     * Element in die Mitte scrollen und anklicken; oben am Rand läge es unter dem fixierten Header.
     */
    protected function clickCentered(Browser $browser, string $selector): Browser
    {
        $browser->waitFor($selector)
            ->script('document.querySelector('.json_encode($selector).').scrollIntoView({ block: "center" })');

        return $browser->click($selector);
    }
}
