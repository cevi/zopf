<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\User;

class ActionPolicy
{
    /**
     * Aktion starten oder abschliessen: Aktionen der eigenen Gruppe (wie in der Aktionen-Tabelle), Admin alle.
     */
    public function complete(User $user, Action $action): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $action->group_id === $user->group_id);
    }
}
