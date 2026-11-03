<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\GroupUser;
use App\Models\User;

class ActionPolicy
{
    /**
     * Aktion starten oder abschliessen: Aktionen der eigenen Gruppe (wie in der Aktionen-Tabelle), Admin alle.
     */
    public function complete(User $user, Action $action): bool
    {
        return $this->manage($user, $action);
    }

    /**
     * Aktion bearbeiten oder löschen: wie abschliessen.
     */
    public function manage(User $user, Action $action): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $user->group_id !== null && $action->group_id === $user->group_id);
    }

    /**
     * In die Aktion wechseln: Aktionen der aktuellen Gruppe (wie das Menü), die globale Aktion, Admin alle.
     * Die aktuelle Gruppe selbst ist nur über eine Mitgliedschaft erreichbar (GroupPolicy::switchTo).
     */
    public function switchTo(User $user, Action $action): bool
    {
        if ($user->isAdmin() || $action->global) {
            return true;
        }

        return $user->group_id !== null
            && $action->group_id === $user->group_id
            && GroupUser::where('group_id', $user->group_id)->where('user_id', $user->id)->exists();
    }
}
