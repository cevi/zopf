<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\ActionScenario;

/**
 * Grundaufbau: Browser erreicht die App, Login funktioniert, Testdaten aus der SQLite-Datei.
 */
class SetupTest extends DuskTestCase
{
    public function test_leader_sees_home_page(): void
    {
        $s = ActionScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->leader)
                ->visit('/home')
                ->assertSee($s->route->name);
        });
    }
}
