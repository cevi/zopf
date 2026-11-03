<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;

/**
 * Jede Person kann sich registrieren und eine eigene Gruppe anlegen: Rollen allein sind nicht vertrauenswürdig,
 * es zählen Mitgliedschaft und Besitz.
 */
class GroupPolicy
{
    /**
     * In die Gruppe wechseln: Mitglieder (wie das Menü), die globale Gruppe, Admin alle.
     */
    public function switchTo(User $user, Group $group): bool
    {
        return $user->isAdmin()
            || $group->global
            || GroupUser::where('group_id', $group->id)->where('user_id', $user->id)->exists();
    }

    /**
     * Gruppe bearbeiten oder löschen: Besitzer (wie das Menü) und Admin.
     */
    public function manage(User $user, Group $group): bool
    {
        return $user->isAdmin() || ($group->user_id !== null && $group->user_id === $user->id);
    }

    /**
     * Neuer Besitzer: muss Mitglied der Gruppe sein (Admin: beliebig).
     */
    public function assignOwner(User $user, Group $group, int $ownerId): bool
    {
        return $user->isAdmin()
            || GroupUser::where('group_id', $group->id)->where('user_id', $ownerId)->exists();
    }
}
