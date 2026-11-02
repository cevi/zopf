<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Bestellung als übergeben oder hinterlegt markieren:
     * Leiter nur auf der eigenen Route, Aktionsleiter und höher in der eigenen aktuellen Aktion, Admin alle.
     */
    public function deliver(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($order->action_id !== $user->action_id) {
            return false;
        }

        return $user->isActionleader() || ($order->route && $order->route->user_id === $user->id);
    }
}
