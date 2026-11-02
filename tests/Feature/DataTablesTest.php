<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Ajax-Endpunkte der Admin-Tabellen (DataTables, serverseitig), mit denselben Parametern wie die Seiten:
 * Spalten und Standard-Sortierung aus admin/routes/index und admin/orders/index.
 * Seit yajra 12 (Laravel 13) brach das Sortieren nach der Status-Spalte (Array display/sort) mit 500 ab:
 * Die Routen- und die Bestell-Liste blieben leer.
 */
class DataTablesTest extends TestCase
{
    /**
     * Parameter wie DataTables sie sendet. $columns: [name => data], $order: [[Spaltenindex, Richtung]].
     */
    private function params(array $columns, array $order): array
    {
        $cols = [];
        foreach ($columns as $name => $data) {
            $cols[] = ['data' => $data, 'name' => $name, 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']];
        }

        return [
            'draw' => 1,
            'columns' => $cols,
            'order' => array_map(fn ($o) => ['column' => $o[0], 'dir' => $o[1]], $order),
            'start' => 0,
            'length' => 10,
            'search' => ['value' => '', 'regex' => 'false'],
        ];
    }

    private function statusColumn(): array
    {
        return ['_' => 'status.display', 'sort' => 'status.sort'];
    }

    public function test_routes_table_sorts_by_status(): void
    {
        $s = ActionScenario::create();
        $params = $this->params([
            'name' => 'name', 'user' => 'user', 'routetype' => 'routetype', 'zopf_count' => 'zopf_count',
            'order_count' => 'order_count', 'status.sort' => $this->statusColumn(), 'Actions' => 'Actions',
        ], [[5, 'asc'], [0, 'asc']]);

        $response = $this->actingAs($s->actionleader)->getJson(route('routes.CreateDataTables', $params))->assertOk();

        // Geplant (5) vor Unterwegs (15)
        $this->assertSame([$s->plannedRoute->id, $s->route->id], array_column($response->json('data'), 'id'));
    }

    public function test_orders_table_sorts_by_status(): void
    {
        $s = ActionScenario::create();
        // Zweite offene Bestellung ohne Route: gleich in Abholen und Route, damit nach Status verglichen wird
        Order::factory()->create(['action_id' => $s->action->id]);
        $params = $this->params([
            'name' => 'name', 'firstname' => 'firstname', 'street' => 'street', 'plz' => 'plz', 'city' => 'city',
            'quantity' => 'quantity', 'route' => 'route', 'pick_up' => 'pick_up', 'comments' => 'comments',
            'status.sort' => $this->statusColumn(), 'checkbox' => 'checkbox', 'Actions' => 'Actions',
        ], [[7, 'asc'], [6, 'asc'], [9, 'asc'], [3, 'asc'], [2, 'asc'], [0, 'asc']]);

        $this->actingAs($s->actionleader)
            ->getJson(route('orders.CreateDataTables', $params))
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }
}
