<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Aktionen, die Daten ändern, gehen nur per POST (mit CSRF-Token).
 * Per GET liessen sie sich über einen Link oder ein <img> auf einer fremden Seite auslösen.
 */
class SideEffectRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Kein Broadcast an Pusher
        Event::fake();
    }

    public static function routes(): array
    {
        return [
            'Bestellung übergeben' => ['home.delivered', 'leader', fn (ActionScenario $s) => $s->orderOnRoute->id],
            'Bestellung hinterlegt' => ['home.deposited', 'leader', fn (ActionScenario $s) => $s->orderOnRoute->id],
            'Route: alle ausliefern' => ['routes.deliverAll', 'actionleader', fn (ActionScenario $s) => $s->route->id],
            'Aktion abschliessen' => ['actions.complete', 'actionleader', fn (ActionScenario $s) => $s->action->id],
        ];
    }

    #[DataProvider('routes')]
    public function test_get_is_not_allowed_and_changes_nothing(string $route, string $role, \Closure $param): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->users()[$role])
            ->get(route($route, $param($s)))
            ->assertMethodNotAllowed();

        $this->assertSame(config('status.order_unterwegs'), $s->orderOnRoute->fresh()->order_status_id);
        $this->assertSame($s->action->action_status_id, $s->action->fresh()->action_status_id);
    }

    public function test_leader_marks_order_as_delivered(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)
            ->post(route('home.delivered', $s->orderOnRoute->id))
            ->assertRedirect();

        $this->assertSame(config('status.order_ausgeliefert'), $s->orderOnRoute->fresh()->order_status_id);
    }

    public function test_leader_marks_order_as_deposited(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)
            ->post(route('home.deposited', $s->orderOnRoute->id))
            ->assertRedirect();

        $this->assertSame(config('status.order_hinterlegt'), $s->orderOnRoute->fresh()->order_status_id);
    }

    public function test_actionleader_delivers_all_orders_of_route(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('routes.deliverAll', $s->route))
            ->assertRedirect();

        $this->assertSame(config('status.order_ausgeliefert'), $s->orderOnRoute->fresh()->order_status_id);
    }

    public function test_actionleader_completes_action(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('actions.complete', $s->action))
            ->assertRedirect('/admin/actions');

        $this->assertSame(config('status.action_abgeschlossen'), $s->action->fresh()->action_status_id);
    }

    public function test_complete_ignores_request_fields(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('actions.complete', $s->action), [
                'group_id' => $other->group->id,
                'APIKey' => 'fremd',
                'demo' => true,
                'total_amount' => 9999,
            ]);

        $action = $s->action->fresh();
        $this->assertSame($s->group->id, $action->group_id);
        $this->assertNotSame('fremd', $action->APIKey);
        $this->assertFalse($action->demo);
        $this->assertNotEquals(9999, $action->total_amount);
    }

    public function test_leader_route_page_uses_post_forms(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)
            ->get(route('home.routes', $s->route->id))
            ->assertOk()
            ->assertSee('<form method="POST" action="'.route('home.delivered', $s->orderOnRoute->id).'"', false)
            ->assertSee('<form method="POST" action="'.route('home.deposited', $s->orderOnRoute->id).'"', false)
            ->assertDontSee('href="'.route('home.delivered', $s->orderOnRoute->id).'"', false);
    }

    public function test_route_overview_uses_post_form(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->get(route('routes.overview', $s->route->id))
            ->assertOk()
            ->assertSee('<form method="POST" action="'.route('routes.deliverAll', $s->route).'"', false);
    }
}
