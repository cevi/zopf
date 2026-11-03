<?php

namespace Tests\Feature;

use App\Mail\FeedbackCreated;
use App\Models\Feedback;
use Illuminate\Support\Facades\Mail;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Seite "Rückmeldungen / Änderungen" (admin/changes): Rückmeldung speichern und per Mail melden.
 */
class FeedbackTest extends TestCase
{
    public function test_groupleader_sees_changes_page(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->get('/admin/changes')
            ->assertOk()
            ->assertSee('action="'.route('feedback.store').'"', false);
    }

    public function test_groupleader_sends_feedback(): void
    {
        Mail::fake();
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->post(route('feedback.store'), ['feedback' => 'Bitte Export nach PLZ', 'user_id' => 999])
            ->assertRedirect('admin/changes');

        $feedback = Feedback::sole();
        $this->assertSame('Bitte Export nach PLZ', $feedback->feedback);
        $this->assertSame($s->groupleader->id, $feedback->user_id);
        Mail::assertSent(FeedbackCreated::class);
    }

    public function test_leader_cannot_send_feedback(): void
    {
        Mail::fake();
        $s = ActionScenario::create();

        $this->actingAs($s->leader)->post(route('feedback.store'), ['feedback' => 'x']);

        $this->assertSame(0, Feedback::count());
        Mail::assertNothingSent();
    }
}
