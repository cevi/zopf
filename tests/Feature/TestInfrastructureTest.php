<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoActionSeeder;
use Illuminate\Support\Facades\DB;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Sichert das Fundament ab: Test-DB, Seeder, Factories, Szenario und ein Seitenaufruf über den ganzen Stack.
 */
class TestInfrastructureTest extends TestCase
{
    public function test_database_is_sqlite_in_memory()
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }

    public function test_seeder_creates_master_data()
    {
        $this->assertSame(4, Role::count());
        $this->assertTrue(ActionScenario::admin()->isAdmin());
        $this->assertTrue(Group::where('global', true)->exists());
        $this->assertTrue(Action::where('global', true)->exists());
    }

    public function test_scenario_creates_users_with_expected_roles()
    {
        $s = ActionScenario::create();

        $this->assertTrue($s->groupleader->isGroupleader());
        $this->assertFalse($s->groupleader->isAdmin());
        $this->assertTrue($s->actionleader->isActionleader());
        $this->assertFalse($s->actionleader->isGroupleader());
        $this->assertFalse($s->leader->isActionleader());

        foreach ($s->users() as $user) {
            $this->assertTrue($user->action->is($s->action));
            $this->assertTrue(ActionUser::where('action_id', $s->action->id)->where('user_id', $user->id)->exists());
            $this->assertTrue(GroupUser::where('group_id', $s->group->id)->where('user_id', $user->id)->exists());
        }
    }

    public function test_scenario_creates_orders_within_action()
    {
        $s = ActionScenario::create();

        $this->assertSame(3, $s->action->orders()->count());
        $this->assertTrue($s->orderOnRoute->route->user->is($s->leader));
        $this->assertEquals(1, $s->action->center->center);

        foreach ($s->action->orders as $order) {
            $this->assertSame($s->group->id, $order->address->group_id);
        }
    }

    public function test_two_scenarios_are_separated()
    {
        $a = ActionScenario::create();
        $b = ActionScenario::create();

        $this->assertNotSame($a->group->id, $b->group->id);
        $this->assertSame(3, Order::where('action_id', $b->action->id)->count());
    }

    public function test_each_test_starts_with_master_data_only()
    {
        // Nur der Administrator aus dem Seeder, nichts aus vorherigen Tests.
        $this->assertSame(1, User::count());
        $this->assertSame(0, Order::count());
    }

    public function test_demo_seeder_runs()
    {
        $this->seed(DemoActionSeeder::class);

        $this->assertTrue(User::where('username', 'aktionschef@demo')->where('demo', true)->exists());
        $this->assertTrue(User::where('username', 'leiter1@demo')->where('demo', true)->exists());
    }

    public function test_welcome_page_renders()
    {
        $this->get('/')->assertOk();
    }

    public function test_leader_sees_home_page()
    {
        $s = ActionScenario::create();

        $this->actingAs($s->leader)->get('/home')->assertOk();
    }
}
