<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * JSON-Endpunkte ohne fremde Personendaten.
 * - Benutzersuche ("Bestehenden Leiter hinzufügen"): nur Aktionsleiter und höher, Teilsuche in der E-Mail, Antwort ohne E-Mail.
 * - Zahlen fürs Dashboard (icon_array): nur die eigene aktuelle Aktion, Admin alle.
 */
class DataLeaksTest extends TestCase
{
    public function test_user_search_returns_no_email(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $result = $this->actingAs($a->actionleader)
            // Gesucht wird in der E-Mail (User::$searchable), Teilbegriff genügt
            ->getJson(route('searchajaxuser', ['term' => strstr($b->leader->email, '@', true)]))
            ->assertOk()
            ->json();

        $this->assertContains($b->leader->username, array_column($result, 'username'));
        $this->assertStringNotContainsString('@', json_encode($result));
        foreach ($result as $row) {
            $this->assertSame(['username', 'id'], array_keys($row));
        }
    }

    public function test_user_search_finds_by_full_email_without_returning_it(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $result = $this->actingAs($a->actionleader)->getJson(route('searchajaxuser', ['term' => $b->leader->email]))->json();

        $this->assertContains($b->leader->id, array_column($result, 'id'));
        $this->assertStringNotContainsString($b->leader->email, json_encode($result));
    }

    public function test_user_search_needs_three_characters(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)->getJson(route('searchajaxuser', ['term' => 'a']))->assertOk()->assertExactJson([]);
    }

    public function test_leader_cannot_search_users(): void
    {
        $s = ActionScenario::create();
        $stranger = User::factory()->leader()->create();

        foreach ([$s->leader, $stranger] as $user) {
            $this->actingAs($user->fresh())->getJson(route('searchajaxuser', ['term' => 'example']))->assertRedirect();
        }
    }

    public function test_icon_array_of_foreign_action_is_forbidden(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)
            ->getJson(route('icon_array.get', ['action' => ['id' => $b->action->id]]))
            ->assertForbidden();
    }

    public function test_icon_array_of_own_action(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->getJson(route('icon_array.get', ['action' => ['id' => $s->action->id]]))
            ->assertOk();
    }
}
