<?php

namespace Tests\Feature\Security;

use App\Models\Order;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Bestellungen und Routen (Namen und Adressen). Regeln wie OrderPolicy/RoutePolicy:
 * - Verwalten (Admin-Bereich): Aktionsleiter und höher in der eigenen aktuellen Aktion, Admin alle.
 * - Leiter-Seiten (Route, Karte): eigene Route; Aktionsleiter und höher alle Routen der eigenen Aktion.
 * - Verantwortliche einer Route nur aus der Aktion; Bestellungen nur Routen derselben Aktion zuweisen.
 * - Gespeichert werden nur die Formularfelder.
 */
class OrdersRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Http::fake(['maps.googleapis.com/*' => Http::response(['status' => 'REQUEST_DENIED', 'routes' => []])]);
    }

    // Leiter-Seiten

    public function test_stranger_cannot_read_foreign_route(): void
    {
        $s = ActionScenario::create();
        $stranger = User::factory()->leader()->create();

        $this->actingAs($stranger)->get(route('home.routes', $s->route->id))->assertForbidden();
        $this->actingAs($stranger->fresh())->get(route('home.maps', $s->route->id))->assertForbidden();
    }

    public function test_leader_reads_only_own_route(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)->get(route('home.routes', $s->route->id))->assertOk();
        $this->actingAs($s->leaderWithoutRoute)->get(route('home.routes', $s->route->id))->assertForbidden();
    }

    public function test_actionleader_reads_routes_of_own_action(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)->get(route('home.routes', $s->route->id))->assertOk();
    }

    // Bestellungen verwalten

    public function test_foreign_order_cannot_be_managed(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $order = $b->openOrder;
        $street = $order->address->street;

        $this->actingAs($a->actionleader)->get(route('orders.edit', $order))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->patch(route('orders.update', $order), $this->orderForm(['street' => 'gekapert']))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->post(route('orders.pickup', $order))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->delete(route('orders.destroy', $order))->assertForbidden();

        $this->assertNotNull(Order::find($order->id));
        $this->assertSame($street, Order::find($order->id)->address->street);
        $this->assertSame(config('status.order_offen'), Order::find($order->id)->order_status_id);
    }

    public function test_actionleader_updates_own_order_with_form_fields_only(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();
        $order = $s->openOrder;

        $this->actingAs($s->actionleader)
            ->patch(route('orders.update', $order), $this->orderForm([
                'quantity' => 5, 'route_id' => $s->plannedRoute->id,
                'action_id' => $other->action->id, 'address_id' => $other->openOrder->address_id, 'group_id' => $other->group->id,
            ]))
            ->assertRedirect('/admin/orders');

        $order = Order::find($order->id);
        $this->assertSame(5, (int) $order->quantity);
        $this->assertSame($s->plannedRoute->id, $order->route_id);
        $this->assertSame($s->action->id, $order->action_id);
        $this->assertSame($s->group->id, $order->address->group_id);
        $this->assertNotSame($other->openOrder->address_id, $order->address_id);
    }

    public function test_order_cannot_be_assigned_to_foreign_route(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)
            ->patch(route('orders.update', $a->openOrder), $this->orderForm(['route_id' => $b->plannedRoute->id]))
            ->assertForbidden();

        $this->assertNull(Order::find($a->openOrder->id)->route_id);
    }

    public function test_order_store_does_not_use_foreign_address(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)->post(route('orders.store'), [
            'address_id' => $b->openOrder->address_id, 'quantity' => 1,
            'firstname' => 'Eva', 'name' => 'Neu', 'street' => 'Weg 1', 'plz' => '8000', 'city' => 'Zürich',
        ]);

        $order = Order::latest('id')->first();
        $this->assertSame($a->action->id, $order->action_id);
        $this->assertNotSame($b->openOrder->address_id, $order->address_id);
        $this->assertSame($a->group->id, $order->address->group_id);
    }

    public function test_create_route_only_with_own_orders_and_routes(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        // fremde Route als Ziel
        $this->actingAs($a->actionleader)->post(route('orders.createRoute'), ['name' => '', 'route_id' => $b->plannedRoute->id, 'id' => [$a->openOrder->id]])->assertForbidden();
        // fremde Bestellung in neue eigene Route
        $this->actingAs($a->actionleader->fresh())->post(route('orders.createRoute'), ['name' => 'Neue Route', 'id' => [$b->openOrder->id, $a->openOrder->id]])->assertOk();

        $this->assertNull(Order::find($b->openOrder->id)->route_id);
        $this->assertSame(Route::where('name', 'Neue Route')->value('id'), Order::find($a->openOrder->id)->route_id);
    }

    // Routen verwalten

    public function test_foreign_route_cannot_be_managed(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $route = $b->plannedRoute;

        $this->actingAs($a->actionleader)->get(route('routes.edit', $route->id))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->get(route('routes.overview', $route->id))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->patch(route('routes.update', $route->id), ['name' => 'gekapert'])->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->post(route('routes.send', $route->id))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->delete(route('routes.destroy', $route->id))->assertForbidden();

        $this->assertSame($route->name, Route::find($route->id)->name);
        $this->assertSame($route->route_status_id, Route::find($route->id)->route_status_id);
    }

    public function test_route_update_with_form_fields_and_own_users_only(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->patch(route('routes.update', $s->plannedRoute->id), ['name' => 'Route Süd', 'user_id' => $s->leaderWithoutRoute->id, 'action_id' => $other->action->id, 'photo' => '../../.env'])
            ->assertRedirect('/admin/routes');

        $route = Route::find($s->plannedRoute->id);
        $this->assertSame('Route Süd', $route->name);
        $this->assertSame($s->leaderWithoutRoute->id, $route->user_id);
        $this->assertSame($s->action->id, $route->action_id);
        $this->assertNull($route->photo);

        $this->actingAs($s->actionleader->fresh())
            ->patch(route('routes.update', $s->plannedRoute->id), ['name' => 'Route Süd', 'user_id' => $other->leader->id])
            ->assertForbidden();
    }

    public function test_route_store_with_own_users_only(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)->post(route('routes.store'), ['name' => 'Fremd zugeteilt', 'user_id' => $b->leader->id])->assertForbidden();

        $this->assertFalse(Route::where('name', 'Fremd zugeteilt')->exists());
    }

    public function test_assign_and_remove_only_own_orders(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        // fremde Bestellung in eigene Route
        $this->actingAs($a->actionleader)->post(route('routes.AssignOrders'), ['route_id' => $a->plannedRoute->id, 'id' => [$b->openOrder->id, $a->openOrder->id]]);
        $this->assertNull(Order::find($b->openOrder->id)->route_id);
        $this->assertSame($a->plannedRoute->id, Order::find($a->openOrder->id)->route_id);

        // eigene Bestellung in fremde Route
        $this->actingAs($a->actionleader->fresh())->post(route('routes.AssignOrders'), ['route_id' => $b->plannedRoute->id, 'id' => [$a->openOrder->id]])->assertForbidden();

        // fremde Bestellung aus ihrer Route entfernen
        $this->actingAs($a->actionleader->fresh())->patch(route('routes.RemoveOrder', $b->orderOnRoute->id), ['order_id' => $b->orderOnRoute->id, 'route_id' => $b->route->id])->assertForbidden();
        $this->assertSame($b->route->id, Order::find($b->orderOnRoute->id)->route_id);
    }

    public function test_actionleader_manages_own_route(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)->get(route('routes.edit', $s->plannedRoute->id))->assertOk();
        $this->actingAs($s->actionleader->fresh())->post(route('routes.send', $s->plannedRoute->id))->assertRedirect('/admin/routes');

        $this->assertSame(config('status.route_vorbereitet'), Route::find($s->plannedRoute->id)->route_status_id);
    }

    private function orderForm(array $values): array
    {
        return $values + [
            'firstname' => 'Anna', 'name' => 'Muster', 'street' => 'Weg 1', 'plz' => '8000', 'city' => 'Zürich',
            'quantity' => 2, 'route_id' => '', 'order_status_id' => config('status.order_offen'), 'comments' => '',
        ];
    }
}
