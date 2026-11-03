<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Address;
use App\Models\Order;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'quantity' => fake()->numberBetween(1, 5),
            'action_id' => Action::factory(),
            'order_status_id' => config('status.order_offen'),
            'pick_up' => false,
            'comments' => fake()->sentence(),
        ];
    }

    public function configure()
    {
        // Adresse in der Gruppe der Aktion, wie beim Import.
        return $this->afterMaking(function (Order $order) {
            if (! $order->address_id) {
                $order->address_id = Address::factory()->create([
                    'group_id' => Action::find($order->action_id)->group_id,
                ])->id;
            }
        });
    }

    /**
     * Der Route zuweisen (gleiche Aktion), Status "unterwegs".
     */
    public function onRoute(Route $route)
    {
        return $this->state([
            'action_id' => $route->action_id,
            'route_id' => $route->id,
            'order_status_id' => config('status.order_unterwegs'),
        ]);
    }

    public function pickUp()
    {
        return $this->state(['pick_up' => true]);
    }
}
