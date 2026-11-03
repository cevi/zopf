<?php

namespace App\Policies;

use App\Models\ActionUser;
use App\Models\Route;
use App\Models\User;

class RoutePolicy
{
    /**
     * Leiter-Seiten (Route, Karte): eigene Route; Aktionsleiter und höher alle Routen der eigenen Aktion, Admin alle.
     */
    public function follow(User $user, Route $route): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->action_id === null || $route->action_id !== $user->action_id) {
            return false;
        }

        return $user->isActionleader() || $route->user_id === $user->id;
    }

    /**
     * Route verwalten (bearbeiten, starten, löschen, Bestellungen zuweisen): wie ansehen.
     */
    public function manage(User $user, Route $route): bool
    {
        return $this->view($user, $route);
    }

    /**
     * Verantwortliche einer Route: nur Personen der Aktion (Admin: beliebig).
     */
    public function assignLeader(User $user, int $actionId, int $leaderId): bool
    {
        return $user->isAdmin()
            || ActionUser::where('action_id', $actionId)->where('user_id', $leaderId)->exists();
    }

    /**
     * Route ansehen (z. B. Routenblatt): Routen der eigenen aktuellen Aktion, Admin alle.
     */
    public function view(User $user, Route $route): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $route->action_id === $user->action_id);
    }

    /**
     * "Alle Bestellungen abschliessen": Routen der eigenen aktuellen Aktion, Admin alle.
     */
    public function deliverAll(User $user, Route $route): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $route->action_id === $user->action_id);
    }
}
