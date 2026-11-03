<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Dom\HTMLDocument;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Admin-Tabellen (DataTables rawColumns) geben Namen als HTML aus. Namen kommen aus Registrierung, Import,
 * Bestellformularen: Ein Wert mit HTML oder Attributen darf nur als Text erscheinen.
 */
class HtmlOutputTest extends TestCase
{
    /** greift Text, unquotierte und quotierte Attribute an */
    private const EVIL = 'x onmouseover=alert(1) "><img src=x onerror=alert(2)>';

    private const ALLOWED = [
        'a' => ['href', 'class', 'title', 'data-remote'],
        'input' => ['type', 'name', 'class', 'value', 'onclick'],
        'img' => ['class', 'alt', 'src'],
        'div' => ['class'],
        'i' => ['class', 'style'],
    ];

    private function assertSafeHtml(string $html, string $where): void
    {
        $doc = HTMLDocument::createFromString('<!DOCTYPE html><body>'.$html.'</body>', LIBXML_NOERROR);
        foreach ($doc->body->querySelectorAll('*') as $element) {
            $tag = strtolower($element->tagName);
            $this->assertArrayHasKey($tag, self::ALLOWED, "$where: unerwartetes Element <$tag> in: $html");
            foreach ($element->attributes as $attribute) {
                $this->assertContains($attribute->name, self::ALLOWED[$tag], "$where: unerwartetes Attribut $attribute->name in: $html");
            }
        }
    }

    private function cells(string $route, User $user): array
    {
        return $this->actingAs($user)->getJson(route($route, ['draw' => 1, 'start' => 0, 'length' => 100]))->assertOk()->json('data');
    }

    public function test_orders_table_escapes_names(): void
    {
        $s = ActionScenario::create();
        $s->orderOnRoute->address->update(['firstname' => self::EVIL, 'name' => self::EVIL]);
        $s->route->update(['name' => self::EVIL]);

        foreach ($this->cells('orders.CreateDataTables', $s->actionleader) as $row) {
            foreach (['firstname', 'route', 'Actions', 'checkbox'] as $column) {
                $this->assertSafeHtml((string) $row[$column], "orders.$column");
            }
        }
    }

    public function test_users_table_escapes_names(): void
    {
        $s = ActionScenario::create();
        $s->leader->update(['username' => self::EVIL]);

        foreach ($this->cells('users.CreateDataTables', $s->groupleader) as $row) {
            foreach (['username', 'picture'] as $column) {
                $this->assertSafeHtml((string) $row[$column], "users.$column");
            }
        }
    }

    public function test_route_modal_table_is_safe(): void
    {
        $s = ActionScenario::create();
        $s->openOrder->address->update(['firstname' => self::EVIL]);

        foreach ($this->cells('routes.CreateModalDataTables', $s->actionleader) as $row) {
            $this->assertSafeHtml((string) $row['checkbox'], 'routes.modal.checkbox');
            $this->assertStringNotContainsString('<img', (string) $row['firstname']);
        }
    }

    public function test_map_info_window_escapes_addresses(): void
    {
        $s = ActionScenario::create();

        // Info-Fenster entstehen im Browser (Google Maps, im Test gesperrt): Maskierung im Skript festhalten
        $this->actingAs($s->leader)
            ->get(route('home.maps', $s->route->id))
            ->assertOk()
            ->assertSee("mapEscape(order.address['firstname'])", false)
            ->assertSee("mapEscape(order.address['street'])", false)
            ->assertDontSee("+ order.address['firstname'] +", false);
    }
}
