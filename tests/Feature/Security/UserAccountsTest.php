<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Profil und Benutzerverwaltung (abgestimmte Regeln, November 2026):
 * - Profil: nur das eigene, nur Benutzername, E-Mail, Passwort, Avatar.
 * - Benutzerverwaltung (Nicht-Admins): nur Benutzer der eigenen aktuellen Gruppe mit gleich vielen oder
 *   weniger Rechten; als Rolle höchstens die eigene. Admin: alles. Sonst 403.
 * Vorher konnte jeder Leiter sich selbst zum Administrator machen und fremde Konten (E-Mail, Passwort) ändern.
 */
class UserAccountsTest extends TestCase
{
    private function admin(): int
    {
        return config('status.role_administrator');
    }

    // Profil

    public function test_leader_updates_own_profile(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)
            ->patch(route('home.update', $s->leader), ['username' => 'neu', 'email' => 'neu@example.com', 'password' => ''])
            ->assertRedirect('/home');

        $this->assertSame('neu', $s->leader->fresh()->username);
        $this->assertSame('neu@example.com', $s->leader->fresh()->email);
    }

    public function test_leader_changes_own_password_with_confirmation(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)->patch(route('home.update', $s->leader), [
            'username' => $s->leader->username, 'email' => $s->leader->email,
            'password' => 'neues-passwort', 'password_confirmation' => 'neues-passwort',
        ]);

        $this->assertTrue(Hash::check('neues-passwort', $s->leader->fresh()->password));
    }

    public function test_profile_ignores_role_and_status(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->leader)->patch(route('home.update', $s->leader), [
            'username' => $s->leader->username, 'email' => $s->leader->email, 'password' => '',
            'role_id' => $this->admin(), 'is_active' => 1, 'group_id' => $other->group->id,
            'action_id' => $other->action->id, 'demo' => true,
        ]);

        $user = $s->leader->fresh();
        $this->assertFalse($user->isAdmin());
        $this->assertSame(config('status.role_leader'), $user->role_id);
        $this->assertSame($s->group->id, $user->group_id);
        $this->assertSame($s->action->id, $user->action_id);
        $this->assertFalse((bool) $user->demo);
    }

    public function test_foreign_profile_is_forbidden(): void
    {
        $s = ActionScenario::create();
        $before = $s->actionleader->only(['username', 'email', 'password']);

        $this->actingAs($s->leader)
            ->patch(route('home.update', $s->actionleader), ['username' => 'gekapert', 'email' => 'boese@example.com', 'password' => 'x', 'password_confirmation' => 'x'])
            ->assertForbidden();

        $this->assertSame($before, $s->actionleader->fresh()->only(['username', 'email', 'password']));
    }

    // Benutzerverwaltung

    public function test_groupleader_edits_leader_of_own_group(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)->get(route('users.edit', $s->leader))->assertOk();

        $this->actingAs($s->groupleader->fresh())
            ->patch(route('users.update', $s->leader), [
                'username' => 'umbenannt', 'email' => $s->leader->email, 'password' => '',
                'role_id' => config('status.role_actionleader'), 'is_active' => 1,
            ])
            ->assertRedirect('/admin/users');

        $this->assertSame('umbenannt', $s->leader->fresh()->username);
        $this->assertSame(config('status.role_actionleader'), $s->leader->fresh()->role_id);
    }

    public function test_role_select_offers_only_allowed_roles(): void
    {
        $s = ActionScenario::create();

        $html = $this->actingAs($s->actionleader)->get(route('users.edit', $s->leader))->assertOk()->getContent();
        preg_match('/<select name="role_id".*?<\/select>/s', $html, $select);
        preg_match_all('/<option value="(\d+)"/', $select[0], $options);

        $this->assertSame(
            [(string) config('status.role_actionleader'), (string) config('status.role_leader')],
            $options[1]
        );
    }

    public function test_nobody_but_admin_can_grant_admin(): void
    {
        $s = ActionScenario::create();

        foreach ([$s->actionleader, $s->groupleader] as $user) {
            $this->actingAs($user->fresh())
                ->patch(route('users.update', $user), ['username' => $user->username, 'email' => $user->email, 'password' => '', 'role_id' => $this->admin()])
                ->assertForbidden();
            $this->assertFalse($user->fresh()->isAdmin());
        }
    }

    public function test_actionleader_cannot_promote_to_groupleader(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->patch(route('users.update', $s->leader), ['username' => $s->leader->username, 'email' => $s->leader->email, 'password' => '', 'role_id' => config('status.role_groupleader')])
            ->assertForbidden();

        $this->assertSame(config('status.role_leader'), $s->leader->fresh()->role_id);
    }

    public function test_actionleader_cannot_edit_groupleader(): void
    {
        $s = ActionScenario::create();
        $before = $s->groupleader->only(['username', 'email', 'password']);

        $this->actingAs($s->actionleader)->get(route('users.edit', $s->groupleader))->assertForbidden();
        $this->actingAs($s->actionleader->fresh())
            ->patch(route('users.update', $s->groupleader), ['username' => 'x', 'email' => 'x@example.com', 'password' => 'x'])
            ->assertForbidden();

        $this->assertSame($before, $s->groupleader->fresh()->only(['username', 'email', 'password']));
    }

    public function test_user_of_other_group_is_forbidden(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $before = $b->leader->only(['username', 'email', 'password']);

        $this->actingAs($a->groupleader)->get(route('users.edit', $b->leader))->assertForbidden();
        $this->actingAs($a->groupleader->fresh())
            ->patch(route('users.update', $b->leader), ['username' => 'gekapert', 'email' => 'boese@example.com', 'password' => 'x'])
            ->assertForbidden();

        $this->assertSame($before, $b->leader->fresh()->only(['username', 'email', 'password']));
    }

    public function test_admin_cannot_be_edited_by_groupleader(): void
    {
        $s = ActionScenario::create();
        $admin = ActionScenario::admin();

        $this->actingAs($s->groupleader)
            ->patch(route('users.update', $admin), ['username' => 'x', 'email' => 'x@example.com', 'password' => 'x'])
            ->assertForbidden();

        $this->assertNotSame('x', $admin->fresh()->username);
    }

    public function test_admin_grants_any_role(): void
    {
        $s = ActionScenario::create();

        $this->actingAs(ActionScenario::admin())
            ->patch(route('users.update', $s->leader), ['username' => $s->leader->username, 'email' => $s->leader->email, 'password' => '', 'role_id' => $this->admin()])
            ->assertRedirect('/admin/users');

        $this->assertTrue($s->leader->fresh()->isAdmin());
    }

    public function test_user_update_ignores_unknown_fields(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->groupleader)->patch(route('users.update', $s->leader), [
            'username' => $s->leader->username, 'email' => $s->leader->email, 'password' => '',
            'demo' => true, 'action_id' => $other->action->id, 'foreign_id' => 4711,
        ]);

        $user = User::find($s->leader->id);
        $this->assertFalse((bool) $user->demo);
        $this->assertNotEquals(4711, $user->foreign_id);
    }

    // Benutzer erfassen und hinzufügen

    public function test_actionleader_cannot_create_admin(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('users.store'), ['username' => 'neuer-admin', 'email' => 'neu@example.com', 'password' => 'geheim123', 'role_id' => $this->admin()])
            ->assertForbidden();

        $this->assertFalse(User::where('username', 'neuer-admin')->exists());
    }

    public function test_actionleader_creates_leader(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->post(route('users.store'), ['username' => 'neuer-leiter', 'email' => 'neu@example.com', 'password' => 'geheim123', 'role_id' => config('status.role_leader')])
            ->assertRedirect('/admin/users');

        $user = User::where('username', 'neuer-leiter')->sole();
        $this->assertSame(config('status.role_leader'), $user->role_id);
        $this->assertSame($s->group->id, $user->group_id);
    }

    public function test_add_cannot_grant_higher_role(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)
            ->post(route('users.add'), ['user_id' => $b->leader->id, 'role_id_add' => $this->admin()])
            ->assertForbidden();

        $this->assertDatabaseMissing('group_users', ['user_id' => $b->leader->id, 'group_id' => $a->group->id]);
        $this->assertDatabaseMissing('action_users', ['user_id' => $b->leader->id, 'role_id' => $this->admin()]);
    }

    public function test_add_with_allowed_role(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)
            ->post(route('users.add'), ['user_id' => $b->leader->id, 'role_id_add' => config('status.role_leader')])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('group_users', ['user_id' => $b->leader->id, 'group_id' => $a->group->id, 'role_id' => config('status.role_leader')]);
    }

    public function test_create_form_offers_only_allowed_roles(): void
    {
        $s = ActionScenario::create();

        $html = $this->actingAs($s->actionleader)->get(route('users.index'))->assertOk()->getContent();

        foreach (['role_id', 'role_id_add'] as $name) {
            preg_match('/<select name="'.$name.'".*?<\/select>/s', $html, $select);
            preg_match_all('/<option value="(\d+)"/', $select[0], $options);
            $this->assertSame([(string) config('status.role_actionleader'), (string) config('status.role_leader')], $options[1], $name);
        }
    }
}
