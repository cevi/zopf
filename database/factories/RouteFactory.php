<?php

namespace Database\Factories;

use App\Models\Action;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Route '.fake()->unique()->numberBetween(1, 9999),
            'action_id' => Action::factory(),
            'route_status_id' => config('status.route_geplant'),
            'route_type_id' => config('status.route_type_walking'),
        ];
    }

    public function underway()
    {
        return $this->state(['route_status_id' => config('status.route_unterwegs')]);
    }

    public function completed()
    {
        return $this->state(['route_status_id' => config('status.route_abgeschlossen')]);
    }
}
