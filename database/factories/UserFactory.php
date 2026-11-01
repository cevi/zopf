<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\ActionUser;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role_id' => config('status.role_leader'),
            'is_active' => true,
            'demo' => false,
        ];
    }

    public function configure()
    {
        // Slug wie MakeUserSlug ihn beim Registrieren setzt.
        return $this->afterCreating(function (User $user) {
            if (! $user->slug) {
                $user->update(['slug' => Str::slug($user->username.'-'.$user->id)]);
            }
        });
    }

    public function admin()
    {
        return $this->state(['role_id' => config('status.role_administrator')]);
    }

    public function groupleader()
    {
        return $this->state(['role_id' => config('status.role_groupleader')]);
    }

    public function actionleader()
    {
        return $this->state(['role_id' => config('status.role_actionleader')]);
    }

    public function leader()
    {
        return $this->state(['role_id' => config('status.role_leader')]);
    }

    public function demo()
    {
        return $this->state(['demo' => true]);
    }

    public function unverified()
    {
        return $this->state(['email_verified_at' => null]);
    }

    /**
     * Aktuelle Aktion und Gruppe setzen und die Mitgliedschaften (group_users, action_users) anlegen,
     * mit derselben Rolle wie users.role_id.
     */
    public function inAction(Action $action)
    {
        return $this->state([
            'group_id' => $action->group_id,
            'action_id' => $action->id,
        ])->afterCreating(function (User $user) use ($action) {
            GroupUser::create(['group_id' => $action->group_id, 'user_id' => $user->id, 'role_id' => $user->role_id]);
            ActionUser::create(['action_id' => $action->id, 'user_id' => $user->id, 'role_id' => $user->role_id]);
        });
    }
}
