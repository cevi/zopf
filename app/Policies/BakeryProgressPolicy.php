<?php

namespace App\Policies;

use App\Models\BakeryProgress;
use App\Models\User;

class BakeryProgressPolicy
{
    /**
     * Eintrag im Backstuben-Verlauf verwalten: Aktionsleiter und höher in der eigenen aktuellen Aktion, Admin alle.
     */
    public function manage(User $user, BakeryProgress $progress): bool
    {
        return $user->isAdmin() || ($user->isActionleader() && $user->action_id !== null && $progress->action_id === $user->action_id);
    }
}
