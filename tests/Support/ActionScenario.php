<?php

namespace Tests\Support;

use App\Models\Action;
use App\Models\Group;
use App\Models\Order;
use App\Models\Route;
use App\Models\User;

/**
 * Eine Gruppe (Verein) mit einer laufenden Zopfaktion und allen Rollen.
 *
 * Für Mandanten-Trennung zwei Szenarien bauen: $a = ActionScenario::create(); $b = ActionScenario::create();
 */
class ActionScenario
{
    public Group $group;

    public Action $action;

    public User $groupleader;

    public User $actionleader;

    /** Leiter mit einer Route unterwegs (Lieferant) */
    public User $leader;

    /** Randfall: Leiter der Aktion ohne Route */
    public User $leaderWithoutRoute;

    /** Route des Leiters, unterwegs */
    public Route $route;

    /** Route in Planung, noch niemandem zugewiesen */
    public Route $plannedRoute;

    public Order $orderOnRoute;

    /** Offene Bestellung ohne Route */
    public Order $openOrder;

    /** Bestellung zum Abholen in der Backstube */
    public Order $pickUpOrder;

    public static function create(): self
    {
        $s = new self;

        $s->group = Group::factory()->create();
        $s->action = Action::factory()->create(['group_id' => $s->group->id]);

        $s->groupleader = User::factory()->groupleader()->inAction($s->action)->create();
        $s->actionleader = User::factory()->actionleader()->inAction($s->action)->create();
        $s->leader = User::factory()->leader()->inAction($s->action)->create();
        $s->leaderWithoutRoute = User::factory()->leader()->inAction($s->action)->create();

        $s->group->update(['user_id' => $s->groupleader->id]);
        $s->action->update(['user_id' => $s->actionleader->id]);

        $s->route = Route::factory()->underway()->create([
            'action_id' => $s->action->id,
            'user_id' => $s->leader->id,
        ]);
        $s->plannedRoute = Route::factory()->create(['action_id' => $s->action->id]);

        $s->orderOnRoute = Order::factory()->onRoute($s->route)->create(['sequence' => 1]);
        $s->openOrder = Order::factory()->create(['action_id' => $s->action->id]);
        $s->pickUpOrder = Order::factory()->pickUp()->create(['action_id' => $s->action->id]);

        return $s;
    }

    /**
     * Benutzer pro Rolle, z. B. für Data Provider (Schlüssel als Rollenname).
     *
     * @return array<string, User>
     */
    public function users(): array
    {
        return [
            'groupleader' => $this->groupleader,
            'actionleader' => $this->actionleader,
            'leader' => $this->leader,
            'leaderWithoutRoute' => $this->leaderWithoutRoute,
        ];
    }

    /**
     * Administrator aus dem BasisdatenSeeder (feste ID, Model-Events von Action/Group hängen daran).
     */
    public static function admin(): User
    {
        return User::findOrFail(config('status.Administrator'));
    }
}
