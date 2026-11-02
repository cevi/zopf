<?php

namespace Tests\Feature;

use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * JSON-Endpunkte, die die Seiten per Ajax aufrufen. Ohne die erwarteten Parameter gab es 500er.
 */
class AjaxEndpointsTest extends TestCase
{
    public function test_orders_datatable_without_filters_lists_own_orders(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->getJson(route('orders.CreateDataTables'))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_search_without_term_returns_placeholder(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->getJson(route('searchajaxuser'))
            ->assertOk();
    }

    public function test_icon_array_without_action_is_not_found(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->getJson(route('icon_array.get'))
            ->assertNotFound();
    }

    public function test_icon_array_with_action_returns_icons(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->getJson(route('icon_array.get', ['action' => ['id' => $s->action->id]]))
            ->assertOk()
            ->assertJsonStructure([['id', 'number']]);
    }
}
