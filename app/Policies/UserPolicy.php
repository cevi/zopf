<?php

namespace App\Policies;

use App\Models\GroupUser;
use App\Models\User;

/**
 * Rollen sind hierarchisch: kleinere role_id = mehr Rechte (Administrator 1, Gruppenleiter 5, Aktionsleiter 10, Leiter 20).
 */
class UserPolicy
{
    /**
     * Profil: nur das eigene.
     */
    public function updateProfile(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    /**
     * Benutzerverwaltung: Admin alle; sonst Benutzer der eigenen aktuellen Gruppe mit gleich vielen oder weniger Rechten.
     */
    public function manage(User $user, User $target): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if (! $user->isActionleader() || ! $user->group_id) {
            return false;
        }
        $inOwnGroup = GroupUser::where('group_id', $user->group_id)->where('user_id', $target->id)->exists();

        return $inOwnGroup && (int) $target->role_id >= (int) $user->role_id;
    }

    /**
     * Rolle vergeben: Admin jede, sonst höchstens die eigene.
     */
    public function assignRole(User $user, int $roleId): bool
    {
        return $user->isAdmin() || $roleId >= (int) $user->role_id;
    }
}
