<?php

namespace Tests\Feature\Security;

use App\Models\BakeryProgress;
use App\Models\Help;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Backstuben-Verlauf: nur Einträge der eigenen Aktion, nur Formularfelder.
 * Hilfe-Artikel: nur Admin (wie das Menü). Die Texte erscheinen allen als HTML; jede registrierte Person kann
 * Aktionsleiter werden (eigene Gruppe und Aktion) und hätte sonst Skripte bei allen, auch Admins, einschleusen können.
 */
class ProgressHelpsTest extends TestCase
{
    private function progress(ActionScenario $s): BakeryProgress
    {
        return BakeryProgress::create([
            'action_id' => $s->action->id, 'user_id' => $s->actionleader->id, 'when' => '08:00:00',
            'raw_material' => 1, 'dough' => 1, 'braided' => 1, 'baked' => 1, 'delivered' => 1, 'total' => 5,
        ]);
    }

    private function form(array $values = []): array
    {
        return $values + ['when' => '09:00', 'raw_material' => 2, 'dough' => 2, 'braided' => 2, 'baked' => 2, 'delivered' => 2];
    }

    public function test_foreign_progress_cannot_be_managed(): void
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();
        $progress = $this->progress($b);

        $this->actingAs($a->actionleader)->get(route('progress.edit', $progress))->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->patch(route('progress.update', $progress), $this->form())->assertForbidden();
        $this->actingAs($a->actionleader->fresh())->delete(route('progress.destroy', $progress))->assertForbidden();

        $this->assertSame(5, (int) BakeryProgress::find($progress->id)->total);
    }

    public function test_actionleader_updates_own_progress_with_form_fields_only(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();
        $progress = $this->progress($s);

        $this->actingAs($s->actionleader)
            ->patch(route('progress.update', $progress), $this->form(['action_id' => $other->action->id, 'total' => 999]))
            ->assertRedirect('/admin/progress');

        $progress = BakeryProgress::find($progress->id);
        $this->assertSame(10, (int) $progress->total);
        $this->assertSame($s->action->id, $progress->action_id);
    }

    public function test_progress_store_stays_in_own_action(): void
    {
        $s = ActionScenario::create();
        $other = ActionScenario::create();

        $this->actingAs($s->actionleader)->post(route('progress.store'), $this->form(['action_id' => $other->action->id, 'user_id' => $other->actionleader->id]));

        $progress = BakeryProgress::sole();
        $this->assertSame($s->action->id, $progress->action_id);
        $this->assertSame($s->actionleader->id, $progress->user_id);
    }

    public static function helpRequests(): array
    {
        return [
            'Liste' => ['get', 'helps.index'],
            'Erfassen' => ['post', 'helps.store'],
            'Bearbeiten' => ['get', 'helps.edit'],
            'Speichern' => ['patch', 'helps.update'],
            'Löschen' => ['delete', 'helps.destroy'],
        ];
    }

    #[DataProvider('helpRequests')]
    public function test_only_admin_manages_helps(string $method, string $route): void
    {
        $s = ActionScenario::create();
        $help = Help::create(['title' => 'Dashboard', 'content' => 'Original']);
        $url = in_array($route, ['helps.index', 'helps.store']) ? route($route) : route($route, $help);

        $this->actingAs($s->groupleader)->$method($url, ['title' => 'Dashboard', 'content' => '<script>alert(1)</script>'])->assertRedirect();

        $this->assertSame('Original', Help::find($help->id)->content);
        $this->assertSame(1, Help::count());
    }

    public function test_admin_edits_help(): void
    {
        $help = Help::create(['title' => 'Dashboard', 'content' => 'Original']);

        $this->actingAs(ActionScenario::admin())
            ->patch(route('helps.update', $help), ['title' => 'Dashboard', 'content' => 'Neu'])
            ->assertRedirect();

        $this->assertSame('Neu', Help::find($help->id)->content);
    }
}
