<?php

namespace Tests\Feature\Security;

use App\Models\Action;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Gruppen und Aktionen (abgestimmte Regeln, November 2026). Jede Person kann sich registrieren und eine
 * eigene Gruppe anlegen; Rollen allein sind deshalb nicht vertrauenswürdig, es zählt die Zugehörigkeit.
 * - Gruppe wechseln: nur Gruppen mit Mitgliedschaft (und die globale), Admin alle.
 * - Aktion wechseln: nur Aktionen der aktuellen Gruppe (und die globale), Admin alle.
 * - Gruppe bearbeiten/löschen: Besitzer und Admin.
 * - Aktion bearbeiten/löschen: Aktionsleiter und höher der eigenen Gruppe, Admin alle.
 * - Aktion erfassen: nur in der eigenen Gruppe. Gespeichert werden nur die Formularfelder.
 */
class GroupsActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Kein Broadcast an Pusher
        Event::fake();
    }

    private function stranger(): User
    {
        // wie frisch registriert: Leiter ohne Gruppe und Aktion
        return User::factory()->leader()->create();
    }

    // Wechseln

    public function test_stranger_cannot_switch_into_foreign_group(): void
    {
        $s = ActionScenario::create();
        $user = $this->stranger();

        $this->actingAs($user)->put(route('home.groups.updateGroup', $s->group))->assertForbidden();

        $this->assertNotSame($s->group->id, $user->fresh()->group_id);
        $this->assertDatabaseMissing('group_users', ['user_id' => $user->id, 'group_id' => $s->group->id]);
    }

    public function test_stranger_cannot_switch_into_foreign_action(): void
    {
        $s = ActionScenario::create();
        $user = $this->stranger();

        $this->actingAs($user)->put(route('admin.actions.updateAction', $s->action))->assertForbidden();

        $this->assertNotSame($s->action->id, $user->fresh()->action_id);
        $this->assertDatabaseMissing('action_users', ['user_id' => $user->id, 'action_id' => $s->action->id]);
    }

    public function test_member_switches_between_own_groups(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        // Leiter aus A ist auch Mitglied in B
        GroupUser::create(['user_id' => $a->leader->id, 'group_id' => $b->group->id, 'role_id' => config('status.role_leader')]);

        $this->actingAs($a->leader)->put(route('home.groups.updateGroup', $b->group))->assertRedirect('/home');

        $this->assertSame($b->group->id, $a->leader->fresh()->group_id);
    }

    public function test_member_switches_into_action_of_own_group(): void
    {
        $s = ActionScenario::create();
        $second = Action::factory()->create(['group_id' => $s->group->id]);

        $this->actingAs($s->leader)->put(route('admin.actions.updateAction', $second))->assertRedirect('/home');

        $this->assertSame($second->id, $s->leader->fresh()->action_id);
    }

    public function test_everybody_may_switch_to_global_group_and_action(): void
    {
        $s = ActionScenario::create();
        $globalGroup = Group::where('global', true)->firstOrFail();
        $globalAction = Action::where('global', true)->firstOrFail();

        $this->actingAs($s->leader)->put(route('home.groups.updateGroup', $globalGroup))->assertRedirect('/home');
        $this->actingAs($s->leader->fresh())->put(route('admin.actions.updateAction', $globalAction))->assertRedirect('/home');

        $this->assertSame($globalAction->id, $s->leader->fresh()->action_id);
    }

    // Gruppen bearbeiten

    public function test_stranger_cannot_edit_foreign_group(): void
    {
        $s = ActionScenario::create();
        $user = $this->stranger();

        $this->actingAs($user)->get(route('home.groups.edit', $s->group))->assertForbidden();
        $this->actingAs($user->fresh())->patch(route('home.groups.update', $s->group), ['name' => 'gekapert'])->assertForbidden();
        $this->actingAs($user->fresh())->patch(route('groups.update', $s->group), ['name' => 'gekapert', 'user_id' => $user->id])->assertRedirect();

        $group = Group::find($s->group->id);
        $this->assertNotSame('gekapert', $group->name);
        $this->assertSame($s->groupleader->id, $group->user_id);
    }

    public function test_actionleader_of_group_cannot_edit_group_he_does_not_own(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)->patch(route('groups.update', $s->group), ['name' => 'gekapert', 'user_id' => $s->actionleader->id])->assertForbidden();
        $this->actingAs($s->actionleader->fresh())->delete(route('groups.destroy', $s->group))->assertForbidden();

        $this->assertSame($s->groupleader->id, Group::find($s->group->id)->user_id);
    }

    public function test_owner_edits_group(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->patch(route('groups.update', $s->group), ['name' => 'Cevi Neu', 'user_id' => $s->actionleader->id, 'global' => true])
            ->assertRedirect('admin/groups');

        $group = Group::find($s->group->id);
        $this->assertSame('Cevi Neu', $group->name);
        $this->assertSame($s->actionleader->id, $group->user_id);
        $this->assertFalse((bool) $group->global);
    }

    public function test_owner_cannot_hand_group_to_non_member(): void
    {
        $s = ActionScenario::create();
        $user = $this->stranger();

        $this->actingAs($s->groupleader)
            ->patch(route('groups.update', $s->group), ['name' => $s->group->name, 'user_id' => $user->id])
            ->assertForbidden();

        $this->assertSame($s->groupleader->id, Group::find($s->group->id)->user_id);
    }

    public function test_group_create_ignores_global_and_foreign_owner(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->actionleader)->post(route('groups.store'), ['name' => 'Neue Gruppe', 'user_id' => $s->groupleader->id, 'global' => true]);

        $group = Group::where('name', 'Neue Gruppe')->sole();
        $this->assertFalse((bool) $group->global);
        $this->assertSame($s->actionleader->id, $group->user_id);
    }

    // Aktionen bearbeiten

    public function test_stranger_cannot_edit_or_delete_foreign_action(): void
    {
        $s = ActionScenario::create();
        $user = $this->stranger();

        $this->actingAs($user)->get(route('actions.edit', $s->action))->assertForbidden();
        $this->actingAs($user->fresh())->patch(route('actions.update', $s->action), $this->actionForm(['name' => 'gekapert']))->assertForbidden();
        $this->actingAs($user->fresh())->delete(route('actions.destroy', $s->action))->assertForbidden();

        $this->assertNotSame('gekapert', Action::find($s->action->id)?->name);
        $this->assertSame($s->action->id, $s->leader->fresh()->action_id);
    }

    public function test_actionleader_of_other_group_cannot_edit_action(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->actingAs($a->actionleader)->patch(route('actions.update', $b->action), $this->actionForm(['name' => 'gekapert']))->assertForbidden();

        $this->assertNotSame('gekapert', Action::find($b->action->id)->name);
    }

    public function test_leader_of_own_group_cannot_edit_action(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)->patch(route('actions.update', $s->action), $this->actionForm(['name' => 'gekapert']))->assertForbidden();
    }

    public function test_actionleader_edits_action_of_own_group(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->actionleader)
            ->patch(route('actions.update', $s->action), $this->actionForm([
                'name' => 'Zopfaktion 2027', 'group_id' => $other->group->id, 'global' => true, 'demo' => true, 'user_id' => $other->actionleader->id,
            ]))
            ->assertRedirect('/admin/actions');

        $action = Action::find($s->action->id);
        $this->assertSame('Zopfaktion 2027', $action->name);
        $this->assertSame($s->group->id, $action->group_id);
        $this->assertFalse((bool) $action->global);
        $this->assertFalse((bool) $action->demo);
        $this->assertSame($s->actionleader->id, $action->user_id);
    }

    public function test_action_create_stays_in_own_group(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->leader)->post(route('actions.store'), $this->actionForm([
            'name' => 'Neue Aktion', 'group_id' => $other->group->id, 'global' => true, 'demo' => true,
        ]));

        $action = Action::where('name', 'Neue Aktion')->sole();
        $this->assertSame($s->group->id, $action->group_id);
        $this->assertFalse((bool) $action->global);
        $this->assertFalse((bool) $action->demo);
    }

    private function actionForm(array $values): array
    {
        return $values + [
            'name' => 'Aktion', 'year' => 2026, 'street' => 'Backstubenweg 1', 'plz' => '8000', 'city' => 'Zürich',
            'APIKey' => 'test-api-key', 'action_status_id' => config('status.action_aktiv'),
        ];
    }
}
