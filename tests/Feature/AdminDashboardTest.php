<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Admin-Dashboard (admin.index) mit Logbuch-Liste und Diagrammen.
 */
class AdminDashboardTest extends TestCase
{
    public function test_groupleader_sees_dashboard(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->get(route('admin.index'))
            ->assertOk();
    }

    public function test_logbook_links_point_to_edit_page(): void
    {
        $s = ActionScenario::create();

        // Die Liste baut die Links im Browser: Platzhalter in der Vorlage-URL, der per JS ersetzt wird.
        $this->actingAs($s->groupleader)
            ->get(route('admin.index'))
            ->assertSee(route('logbooks.edit', ':id'), false);
    }

    public function test_groupleader_without_action_sees_dashboard(): void
    {
        $user = User::factory()->groupleader()->create();
        $this->assertNull($user->action);

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertDontSee('api/action//');
    }
}
