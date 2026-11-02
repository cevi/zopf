<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Wartungsseite für Admins: Artisan-Befehle nur per POST und nur für Admins.
 * Ersetzt die früheren GET-URLs admin/run-migrations, admin/run-migrations-seed und admin/run-deployment.
 */
class MaintenanceTest extends TestCase
{
    public static function oldUrls(): array
    {
        return [
            'run-migrations' => ['admin/run-migrations'],
            'run-migrations-seed' => ['admin/run-migrations-seed'],
            'run-deployment' => ['admin/run-deployment'],
        ];
    }

    #[DataProvider('oldUrls')]
    public function test_old_get_urls_are_gone(string $url): void
    {
        // Vor dem ersten Aufruf faken, damit ein roter Test nicht wirklich migriert oder seedet.
        Artisan::shouldReceive('call')->never();

        $this->get($url)->assertNotFound();
    }

    public function test_admin_sees_maintenance_page(): void
    {
        $this->actingAs(ActionScenario::admin())
            ->get(route('admin.maintenance'))
            ->assertOk()
            ->assertSee('Migrationen ausführen')
            ->assertSee('Keine offenen Migrationen');
    }

    public function test_admin_runs_migrations(): void
    {
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true]);
        Artisan::shouldReceive('output')->andReturn('Nothing to migrate.');

        $this->actingAs(ActionScenario::admin())
            ->post(route('admin.maintenance.migrate'))
            ->assertRedirect(route('admin.maintenance'))
            ->assertSessionHas('maintenance_output', 'Nothing to migrate.');

        $this->actingAs(ActionScenario::admin()->fresh())
            ->get(route('admin.maintenance'))
            ->assertSee('Nothing to migrate.');
    }

    public function test_admin_rebuilds_caches(): void
    {
        Artisan::shouldReceive('call')->once()->with('config:cache');
        Artisan::shouldReceive('call')->once()->with('view:cache');
        Artisan::shouldReceive('output')->andReturn('ok');

        $this->actingAs(ActionScenario::admin())
            ->post(route('admin.maintenance.cache'))
            ->assertRedirect(route('admin.maintenance'));
    }

    public function test_admin_clears_caches(): void
    {
        Artisan::shouldReceive('call')->once()->with('optimize:clear');
        Artisan::shouldReceive('output')->andReturn('ok');

        $this->actingAs(ActionScenario::admin())
            ->post(route('admin.maintenance.clear'))
            ->assertRedirect(route('admin.maintenance'));
    }

    public static function actions(): array
    {
        return [
            'migrate' => ['admin.maintenance.migrate'],
            'cache' => ['admin.maintenance.cache'],
            'clear' => ['admin.maintenance.clear'],
        ];
    }

    #[DataProvider('actions')]
    public function test_guest_cannot_run_actions(string $route): void
    {
        Artisan::shouldReceive('call')->never();

        $this->post(route($route))->assertRedirect();
        $this->get(route('admin.maintenance'))->assertRedirect();
    }

    #[DataProvider('actions')]
    public function test_groupleader_cannot_run_actions(string $route): void
    {
        Artisan::shouldReceive('call')->never();
        $s = ActionScenario::create();

        $this->actingAs($s->groupleader)->post(route($route))->assertRedirect();
        $this->actingAs($s->groupleader->fresh())->get(route('admin.maintenance'))->assertRedirect();
    }

    #[DataProvider('actions')]
    public function test_actions_reject_get(string $route): void
    {
        Artisan::shouldReceive('call')->never();

        $this->actingAs(ActionScenario::admin())
            ->get(route($route))
            ->assertMethodNotAllowed();
    }
}
