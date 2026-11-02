<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\Order;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\ActionScenario;

/**
 * Formulare, die beim Ersatz von Form:: (laravelcollective/html) auf Blade-Komponenten umgebaut wurden:
 * Vorbelegung aus dem Model, Absenden, gespeicherte Werte, Eingaben nach Validierungsfehlern.
 *
 * Nicht hier: Bestellung bearbeiten und Aktion bearbeiten (rufen auf dem Server immer den Geocoder bei Google auf).
 */
class FormsTest extends DuskTestCase
{
    public function test_route_edit_form_is_prefilled_and_saves(): void
    {
        $s = ActionScenario::create();
        $form = 'form[action="'.route('routes.update', $s->route->id).'"]';

        $this->browse(function (Browser $browser) use ($s, $form) {
            $browser->loginAs($s->actionleader)
                ->visit(route('routes.edit', $s->route->id))
                ->assertInputValue($form.' [name="name"]', $s->route->name)
                ->assertSelected($form.' [name="user_id"]', (string) $s->leader->id)
                ->type($form.' [name="name"]', 'Route Nord')
                ->select($form.' [name="route_type_id"]', (string) config('status.route_type_driving'))
                ->press('Route Aktualisieren')
                ->assertPathIs('/admin/routes');
        });

        $route = Route::find($s->route->id);
        $this->assertSame('Route Nord', $route->name);
        $this->assertSame(config('status.route_type_driving'), $route->route_type_id);
        $this->assertSame($s->leader->id, $route->user_id);
    }

    public function test_group_edit_form_saves(): void
    {
        $s = ActionScenario::create();
        $form = 'form[action="'.route('groups.update', $s->group->id).'"]';

        $this->browse(function (Browser $browser) use ($s, $form) {
            $browser->loginAs($s->groupleader)
                ->visit(route('groups.edit', $s->group->id))
                ->assertInputValue($form.' [name="name"]', $s->group->name)
                ->type($form.' [name="name"]', 'Cevi Test')
                ->press('Gruppe aktualisieren')
                ->assertPathIs('/admin/groups');
        });

        $this->assertSame('Cevi Test', Group::find($s->group->id)->name);
    }

    public function test_user_edit_form_changes_role_and_keeps_password(): void
    {
        $s = ActionScenario::create();
        $password = $s->leader->password;
        $form = 'form[action="'.route('users.update', $s->leader->id).'"]';

        $this->browse(function (Browser $browser) use ($s, $form) {
            $browser->loginAs($s->groupleader)
                ->visit(route('users.edit', $s->leader->id))
                ->assertInputValue($form.' [name="username"]', $s->leader->username)
                ->assertSelected($form.' [name="role_id"]', (string) config('status.role_leader'))
                ->assertSelectMissingOption($form.' [name="role_id"]', (string) config('status.role_administrator'))
                ->select($form.' [name="role_id"]', (string) config('status.role_actionleader'))
                ->press('Update Leiter')
                ->assertPathIs('/admin/users');
        });

        $user = User::find($s->leader->id);
        $this->assertSame(config('status.role_actionleader'), $user->role_id);
        $this->assertSame($password, $user->password);
    }

    public function test_profile_keeps_input_after_validation_error(): void
    {
        $s = ActionScenario::create();
        $form = 'form[action="'.route('home.update', $s->leader->id).'"]';

        $this->browse(function (Browser $browser) use ($s, $form) {
            $browser->loginAs($s->leader)
                ->visit(route('home.user', $s->leader->id))
                ->assertInputValue($form.' [name="email"]', $s->leader->email)
                ->type($form.' [name="username"]', 'neuer-name')
                ->type($form.' [name="password"]', 'passwort-eins')
                ->type($form.' [name="password_confirmation"]', 'passwort-zwei')
                ->press('Speichern')
                ->assertPathIs('/user/'.$s->leader->id)
                // Eingabe bleibt nach dem Fehler stehen (old() vor Model), Passwort nie
                ->assertInputValue($form.' [name="username"]', 'neuer-name')
                ->assertInputValue($form.' [name="password"]', '');
        });

        $this->assertSame($s->leader->username, User::find($s->leader->id)->username);
    }

    public function test_dashboard_logbook_entry_is_saved_and_escaped(): void
    {
        $s = ActionScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->actionleader)
                ->visit('/admin');

            // Zeitfeld mit now() vorbelegt, im Format, das ein <input type="time"> annimmt
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $browser->inputValue('when'));

            $browser->select('user_id', (string) $s->leader->id)
                ->type('comments', '<img id="xss" src="x">Zopf ist gebacken')
                ->press('Eintrag Erfassen')
                ->assertPathIs('/admin')
                ->assertMissing('#xss')
                ->assertSee('<img id="xss" src="x">Zopf ist gebacken');

            // Liste wie bei einer Live-Benachrichtigung per Ajax neu aufbauen (escapeHtml)
            $browser->script('UpdateList()');
            $browser->pause(500)->assertMissing('#xss');
        });

        $this->assertTrue(DB::table('notifications')->where('content', 'like', '%Zopf ist gebacken%')->exists());
    }

    public function test_order_for_pick_up_is_created(): void
    {
        $s = ActionScenario::create();
        $count = Order::count();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->actionleader)
                ->visit(route('orders.create'))
                ->type('firstname', 'Anna')
                ->type('name', 'Muster')
                ->check('pick_up')
                ->type('quantity', '3')
                ->press('Adresse Erfassen')
                // Zurück aufs leere Formular, für die nächste Bestellung
                ->assertPathIs('/admin/orders/create')
                ->assertInputValue('firstname', '');
        });

        $this->assertSame($count + 1, Order::count());
        $order = Order::latest('id')->first();
        $this->assertTrue((bool) $order->pick_up);
        $this->assertSame(3, (int) $order->quantity);
        $this->assertSame($s->action->id, $order->action_id);
    }

    public function test_leader_marks_order_as_delivered(): void
    {
        $s = ActionScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->leader)
                ->visit(route('home.routes', $s->route->id))
                // Knöpfe liegen im aufklappbaren Bereich der Bestellung
                ->click('[data-accordion-target="#accordion-collapse-body-0"]')
                ->waitFor('#accordion-collapse-body-0 button[type="submit"]')
                ->press('Übergeben')
                ->assertPathIs('/home');
        });

        $this->assertSame(config('status.order_ausgeliefert'), Order::find($s->orderOnRoute->id)->order_status_id);
    }
}
