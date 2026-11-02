<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Zugriff auf Bestellungen, Routen und Aktionen anderer (abgestimmte Regeln, November 2026):
 * - Bestellung übergeben/hinterlegt: Leiter nur auf der eigenen Route, Aktionsleiter und höher in der eigenen aktuellen Aktion, Admin alle.
 * - Route "Alle Bestellungen abschliessen": Routen der eigenen aktuellen Aktion, Admin alle.
 * - Aktion starten/abschliessen: Aktionen der eigenen Gruppe (wie die Aktionen-Tabelle), Admin alle.
 * Nicht erlaubt → 403, nichts wird geändert.
 */
class ForeignIdsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Kein Broadcast an Pusher
        Event::fake();
    }

    /**
     * [Route, Benutzer aus Szenario $a, Bestellung/Route/Aktion aus $a oder $b]
     */
    public static function forbidden(): array
    {
        return [
            'Leiter übergibt fremde Bestellung' => ['home.delivered', fn ($a) => $a->leader, fn ($a, $b) => $b->orderOnRoute],
            'Leiter hinterlegt fremde Bestellung' => ['home.deposited', fn ($a) => $a->leader, fn ($a, $b) => $b->orderOnRoute],
            'Aktionsleiter übergibt fremde Bestellung' => ['home.delivered', fn ($a) => $a->actionleader, fn ($a, $b) => $b->orderOnRoute],
            'Leiter ohne Route übergibt Bestellung eines anderen Leiters' => ['home.delivered', fn ($a) => $a->leaderWithoutRoute, fn ($a, $b) => $a->orderOnRoute],
            'Leiter ohne Route hinterlegt Bestellung eines anderen Leiters' => ['home.deposited', fn ($a) => $a->leaderWithoutRoute, fn ($a, $b) => $a->orderOnRoute],
            'Aktionsleiter liefert fremde Route aus' => ['routes.deliverAll', fn ($a) => $a->actionleader, fn ($a, $b) => $b->route],
            'Gruppenleiter liefert fremde Route aus' => ['routes.deliverAll', fn ($a) => $a->groupleader, fn ($a, $b) => $b->route],
            'Aktionsleiter schliesst fremde Aktion ab' => ['actions.complete', fn ($a) => $a->actionleader, fn ($a, $b) => $b->action],
            'Gruppenleiter schliesst fremde Aktion ab' => ['actions.complete', fn ($a) => $a->groupleader, fn ($a, $b) => $b->action],
        ];
    }

    #[DataProvider('forbidden')]
    public function test_foreign_objects_are_forbidden(string $route, \Closure $user, \Closure $target): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $object = $target($a, $b);
        $orderStatus = $b->orderOnRoute->order_status_id;
        $ownOrderStatus = $a->orderOnRoute->order_status_id;
        $actionStatus = $b->action->action_status_id;

        $this->actingAs($user($a))
            ->post(route($route, $object->id))
            ->assertForbidden();

        $this->assertSame($orderStatus, $b->orderOnRoute->fresh()->order_status_id);
        $this->assertSame($ownOrderStatus, $a->orderOnRoute->fresh()->order_status_id);
        $this->assertSame($actionStatus, $b->action->fresh()->action_status_id);
    }

    public function test_actionleader_marks_any_order_of_own_action(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('home.deposited', $s->orderOnRoute->id))
            ->assertRedirect();

        $this->assertSame(config('status.order_hinterlegt'), $s->orderOnRoute->fresh()->order_status_id);
    }

    public function test_groupleader_completes_action_of_own_group(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->post(route('actions.complete', $s->action))
            ->assertRedirect('/admin/actions');

        $this->assertSame(config('status.action_abgeschlossen'), $s->action->fresh()->action_status_id);
    }

    public function test_admin_may_act_on_any_group(): void
    {
        $s = ActionScenario::create();
        $admin = ActionScenario::admin();

        $this->actingAs($admin)->post(route('routes.deliverAll', $s->route))->assertRedirect();
        $this->assertSame(config('status.order_ausgeliefert'), $s->orderOnRoute->fresh()->order_status_id);

        $this->actingAs($admin->fresh())->post(route('actions.complete', $s->action))->assertRedirect('/admin/actions');
        $this->assertSame(config('status.action_abgeschlossen'), $s->action->fresh()->action_status_id);
    }

    public function test_admin_may_mark_any_order(): void
    {
        $s = ActionScenario::create();

        $this->actingAs(ActionScenario::admin())
            ->post(route('home.delivered', $s->openOrder->id))
            ->assertRedirect();

        $this->assertSame(config('status.order_ausgeliefert'), $s->openOrder->fresh()->order_status_id);
    }
}
