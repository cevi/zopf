<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Address;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => 'Zopfaktion '.fake()->unique()->numberBetween(1, 9999),
            'year' => (int) date('Y'),
            'group_id' => Group::factory(),
            'action_status_id' => config('status.action_aktiv'),
            'APIKey' => 'test-api-key', // Pflichtfeld im Formular; Google wird in Tests nie aufgerufen
            'global' => false,
            'demo' => false,
        ];
    }

    public function configure()
    {
        // Jede Aktion hat einen Standort (Backstube), der zur Gruppe gehört.
        return $this->afterCreating(function (Action $action) {
            if (! $action->address_id) {
                $center = Address::factory()->create([
                    'name' => $action->name,
                    'firstname' => '',
                    'group_id' => $action->group_id,
                    'center' => true,
                ]);
                $action->update(['address_id' => $center->id]);
            }
        });
    }

    public function planned()
    {
        return $this->state(['action_status_id' => config('status.action_geplant')]);
    }

    public function completed()
    {
        return $this->state(['action_status_id' => config('status.action_abgeschlossen')]);
    }
}
