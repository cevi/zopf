<?php

namespace Tests\Feature;

use App\Models\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Logbuch-Einträge bearbeiten und löschen: nur Einträge der eigenen Aktion, nur die Felder des Formulars.
 */
class LogbookTest extends TestCase
{
    private function entry(Action $action): string
    {
        $id = (string) Str::uuid();
        DB::table('notifications')->insert([
            'id' => $id,
            'type' => 'App\Notifications\CreateLogEntry',
            'notifiable_type' => Action::class,
            'notifiable_id' => $action->id,
            'quantity' => 0,
            'when' => '10:00:00',
            'user' => 'leiter',
            'content' => 'Original',
        ]);

        return $id;
    }

    public function test_actionleader_edits_own_entry(): void
    {
        $s = ActionScenario::create();
        $id = $this->entry($s->action);

        $this->actingAs($s->actionleader)->get(route('logbooks.edit', $id))->assertOk()->assertSee('Original');

        $this->actingAs($s->actionleader->fresh())
            ->patch(route('logbooks.update', $id), ['content' => 'Neu', 'user' => 'leiter', 'when' => '11:00', 'cut' => 0])
            ->assertRedirect('/admin');

        $this->assertSame('Neu', DB::table('notifications')->find($id)->content);
    }

    public function test_foreign_entry_is_not_found(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $id = $this->entry($b->action);

        $this->actingAs($a->actionleader)->get(route('logbooks.edit', $id))->assertNotFound();
        $this->actingAs($a->actionleader->fresh())->patch(route('logbooks.update', $id), ['content' => 'Neu'])->assertNotFound();
        $this->actingAs($a->actionleader->fresh())->delete(route('logbooks.destroy', $id))->assertNotFound();

        $this->assertSame('Original', DB::table('notifications')->find($id)->content);
    }

    public function test_update_only_changes_form_fields(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $id = $this->entry($a->action);

        $this->actingAs($a->actionleader)->patch(route('logbooks.update', $id), [
            'content' => 'Neu',
            'notifiable_id' => $b->action->id,
            'quantity' => 500,
        ]);

        $entry = DB::table('notifications')->find($id);
        $this->assertEquals($a->action->id, $entry->notifiable_id);
        $this->assertEquals(0, $entry->quantity);
    }
}
