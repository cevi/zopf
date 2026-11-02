<?php

namespace App\Policies;

use App\Models\Route;
use App\Models\User;

class RoutePolicy
{
    /**
     * "Alle Bestellungen abschliessen": Routen der eigenen aktuellen Aktion, Admin alle.
     */
    public function deliverAll(User $user, Route $route): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $route->action_id === $user->action_id);
    }
}
