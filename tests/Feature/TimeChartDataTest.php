<?php

namespace Tests\Feature;

use App\Helper\Helper;
use App\Models\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Zeitdiagramm auf dem Dashboard: Zöpfe pro halbe Stunde aus den Logbuch-Einträgen.
 */
class TimeChartDataTest extends TestCase
{
    private function log(Action $action, ?string $when, int $quantity): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\CreateLogEntry',
            'notifiable_type' => Action::class,
            'notifiable_id' => $action->id,
            'quantity' => $quantity,
            'when' => $when,
        ]);
    }

    public function test_rounds_to_nearest_half_hour_and_fills_gaps(): void
    {
        $action = ActionScenario::create()->action;
        $this->log($action, '10:00:00', 5);
        $this->log($action, '10:14:59', 1); // abgerundet auf 10:00
        $this->log($action, '10:15:00', 2); // aufgerundet auf 10:30
        $this->log($action, '11:20:00', 3); // aufgerundet auf 11:30

        $this->assertSame(['10:00', '10:30', '11:00', '11:30'], Helper::GetTimeChartData($action, true));
        $this->assertEquals([6, 2, 0, 3], Helper::GetTimeChartData($action));
    }

    public function test_entries_without_time_are_ignored(): void
    {
        $action = ActionScenario::create()->action;
        $this->log($action, null, 4);
        $this->log($action, '09:00:00', 2);

        $this->assertSame(['09:00'], Helper::GetTimeChartData($action, true));
        $this->assertEquals([2], Helper::GetTimeChartData($action));
    }

    public function test_only_own_action_is_counted(): void
    {
        $a = ActionScenario::create()->action;
        $b = ActionScenario::create()->action;
        $this->log($a, '09:00:00', 2);
        $this->log($b, '09:00:00', 7);

        $this->assertEquals([2], Helper::GetTimeChartData($a));
    }

    public function test_empty_action_returns_zero(): void
    {
        $action = ActionScenario::create()->action;

        $this->assertSame([0], Helper::GetTimeChartData($action, true));
        $this->assertSame([0], Helper::GetTimeChartData($action));
    }

    public function test_without_action_returns_zero(): void
    {
        $this->assertSame([0], Helper::GetTimeChartData(null, true));
        $this->assertSame([0], Helper::GetTimeChartData(null));
        $this->assertSame(0, Helper::GetZopfChartData(null)['total']);
    }
}
