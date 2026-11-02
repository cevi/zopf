<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Routenblatt (admin/routes/{id}/downloadPDF): Karte über Google Maps (Directions, Static Maps).
 * Google wird nie wirklich aufgerufen.
 */
class RoutePdfTest extends TestCase
{
    public function test_google_error_redirects_back_with_message(): void
    {
        Http::fake(['maps.googleapis.com/*' => Http::response(['status' => 'REQUEST_DENIED', 'routes' => []])]);
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->from(route('routes.overview', $s->route->id))
            ->get(route('routes.downloadPDF', $s->route->id))
            ->assertRedirect(route('routes.overview', $s->route->id))
            ->assertSessionHas('error');

        $this->assertNull($s->route->fresh()->photo);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'maps.googleapis.com/maps/api/directions/json'));
    }

    public function test_existing_map_is_reused_without_google(): void
    {
        Http::fake();
        $s = ActionScenario::create();
        $s->route->update(['photo' => 'images/test/karte.png']);

        $this->actingAs($s->actionleader)
            ->get(route('routes.downloadPDF', $s->route->id))
            ->assertOk()
            ->assertSee($s->route->name);

        Http::assertNothingSent();
    }

    public function test_foreign_route_is_forbidden(): void
    {
        Http::fake();
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)
            ->get(route('routes.downloadPDF', $b->route->id))
            ->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_overview_shows_error_message(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->withSession(['error' => 'Karte konnte nicht erstellt werden'])
            ->get(route('routes.overview', $s->route->id))
            ->assertSee('Karte konnte nicht erstellt werden');
    }
}
