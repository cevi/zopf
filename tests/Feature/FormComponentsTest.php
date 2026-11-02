<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Formular-Komponenten (resources/views/components/form), Ersatz für laravelcollective/html (Form::).
 * Wertauflösung wie dort: old() vor explizitem Wert vor Model-Attribut.
 */
class FormComponentsTest extends TestCase
{
    private function withOldInput(array $input): void
    {
        // old() liest über den Request; Blade::render() läuft ohne Request-Session.
        $session = $this->app['session']->driver();
        $session->flashInput($input);
        $this->app['request']->setLaravelSession($session);
    }

    public function test_input_takes_value_from_model(): void
    {
        $html = Blade::render('<x-form.input name="name" :model="$m" />', ['m' => (object) ['name' => 'Route A']]);

        $this->assertStringContainsString('value="Route A"', $html);
        $this->assertStringContainsString('type="text"', $html);
    }

    public function test_explicit_value_wins_over_model(): void
    {
        $html = Blade::render('<x-form.input name="name" value="fest" :model="$m" />', ['m' => (object) ['name' => 'Route A']]);

        $this->assertStringContainsString('value="fest"', $html);
    }

    public function test_old_input_wins_over_value_and_model(): void
    {
        $this->withOldInput(['name' => 'eingegeben']);

        $html = Blade::render('<x-form.input name="name" value="fest" :model="$m" />', ['m' => (object) ['name' => 'Route A']]);

        $this->assertStringContainsString('value="eingegeben"', $html);
    }

    public function test_value_is_escaped(): void
    {
        $html = Blade::render('<x-form.input name="name" :model="$m" />', ['m' => (object) ['name' => '"><script>x</script>']]);

        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_password_never_shows_value(): void
    {
        $html = Blade::render('<x-form.input name="password" type="password" :model="$m" />', ['m' => (object) ['password' => 'geheim']]);

        $this->assertStringNotContainsString('geheim', $html);
    }

    public function test_time_from_carbon_is_formatted(): void
    {
        $html = Blade::render('<x-form.input name="when" type="time" :model="$m" />', ['m' => (object) ['when' => Carbon::parse('2026-11-02 08:30:00')]]);

        $this->assertStringContainsString('value="08:30"', $html);
    }

    public function test_attributes_are_passed_through(): void
    {
        $html = Blade::render('<x-form.input name="name" id="name" class="mb-6" required />');

        $this->assertStringContainsString('id="name"', $html);
        $this->assertStringContainsString('class="mb-6"', $html);
        $this->assertStringContainsString('required', $html);
    }

    public function test_textarea_takes_value_from_model(): void
    {
        $html = Blade::render('<x-form.textarea name="content" :model="$m" />', ['m' => (object) ['content' => 'Text']]);

        $this->assertMatchesRegularExpression('/<textarea name="content"[^>]*rows="10"[^>]*>Text<\/textarea>/', $html);
    }

    public function test_select_marks_model_value_as_selected(): void
    {
        $html = Blade::render('<x-form.select name="user_id" :options="$o" :model="$m" />', [
            'o' => ['' => 'Wähle Leiter', 1 => 'Anna', 2 => 'Ben'],
            'm' => (object) ['user_id' => 2],
        ]);

        $this->assertMatchesRegularExpression('/<option value="2"\s+selected[^>]*>Ben<\/option>/', $html);
        $this->assertDoesNotMatchRegularExpression('/<option value="1"\s+selected/', $html);
    }

    public function test_select_uses_old_input(): void
    {
        $this->withOldInput(['user_id' => '1']);

        $html = Blade::render('<x-form.select name="user_id" :options="$o" :model="$m" />', [
            'o' => [1 => 'Anna', 2 => 'Ben'],
            'm' => (object) ['user_id' => 2],
        ]);

        $this->assertMatchesRegularExpression('/<option value="1"\s+selected/', $html);
    }

    public function test_checkbox_state(): void
    {
        $render = fn (array $data, string $extra = '') => Blade::render('<x-form.checkbox name="pick_up" value="yes" '.$extra.' :model="$m" />', $data);

        $this->assertStringContainsString('checked', $render(['m' => (object) ['pick_up' => true]]));
        $this->assertStringNotContainsString('checked', $render(['m' => (object) ['pick_up' => false]]));
        $this->assertStringNotContainsString('checked', $render(['m' => (object) ['pick_up' => true]], ':checked="false"'));
    }

    public function test_unchecked_checkbox_stays_unchecked_after_failed_submit(): void
    {
        // Nach dem Absenden ohne Haken fehlt das Feld in old(), obwohl das Model true hat.
        $this->withOldInput(['name' => 'x']);

        $html = Blade::render('<x-form.checkbox name="pick_up" value="yes" :model="$m" />', ['m' => (object) ['pick_up' => true]]);

        $this->assertStringNotContainsString('checked', $html);
    }

    public function test_edit_page_renders_form_with_model_values(): void
    {
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)
            ->get(route('routes.edit', $s->route))
            ->assertOk()
            ->assertSee('action="'.route('routes.update', $s->route).'"', false)
            ->assertSee('name="_method" value="PATCH"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('value="'.e($s->route->name).'"', false);
    }
}
