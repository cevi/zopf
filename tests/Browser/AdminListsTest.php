<?php

namespace Tests\Browser;

use App\Models\Order;
use Facebook\WebDriver\Exception\NoSuchAlertException;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DuskTestCase;
use Tests\Support\ActionScenario;

/**
 * Admin-Listen mit DataTables (serverseitig, Ajax): Zeilen erscheinen, kein DataTables-Alert, keine JS-Fehler.
 * Fand: Routen- und Bestell-Liste blieben seit Laravel 13 leer (Sortieren nach der Status-Spalte, Ajax 500).
 */
class AdminListsTest extends DuskTestCase
{
    /**
     * Bekanntes Rauschen in der Konsole, kein Fehler der Seite.
     */
    private const IGNORED = [
        'pusher.com',              // im Test gesperrt (DuskTestCase)
        'has not been initialized', // Flowbite: Drawer ohne Auslöser auf der Seite
    ];

    public static function lists(): array
    {
        return [
            'Routen' => ['/admin/routes', fn (ActionScenario $s) => $s->route->name],
            'Bestellungen' => ['/admin/orders', fn (ActionScenario $s) => $s->openOrder->address->name],
            'Leiter' => ['/admin/users', fn (ActionScenario $s) => $s->leader->username],
            'Aktionen' => ['/admin/actions', fn (ActionScenario $s) => $s->action->name],
            'Gruppen' => ['/admin/groups', fn (ActionScenario $s) => $s->group->name],
        ];
    }

    #[DataProvider('lists')]
    public function test_list_loads_rows(string $url, \Closure $expected): void
    {
        $s = ActionScenario::create();
        // Zweite offene Bestellung ohne Route: Die Bestell-Liste vergleicht dann auch die Status-Spalte
        Order::factory()->create(['action_id' => $s->action->id]);

        $this->browse(function (Browser $browser) use ($s, $url, $expected) {
            $browser->loginAs($s->groupleader)->visit($url);

            // DataTables meldet Ajax-Fehler per alert()
            $browser->pause(1000);
            try {
                $alert = $browser->driver->switchTo()->alert()->getText();
                $browser->acceptDialog();
                $this->fail("DataTables-Fehler auf $url: $alert");
            } catch (NoSuchAlertException) {
            }

            $browser->waitForText($expected($s));

            $errors = array_values(array_filter(
                $this->consoleErrors($browser),
                fn ($e) => ! collect(self::IGNORED)->contains(fn ($ignored) => str_contains($e, $ignored))
            ));
            $this->assertSame([], $errors, "JS-Fehler auf $url");
        });
    }
}
