<?php

namespace Database\Seeders;

use App\Events\NotificationCreate;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Address;
use App\Models\BakeryProgress;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Order;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $user = User::factory()->create([
            'username' => 'aktionschef@demo',
            'email' => 'aktionschef@demo',
            'slug' => 'aktionschef@demo',
            'password' => Hash::make('aktionschef@demo'),
            'role_id' => config('status.role_groupleader'),
            'is_active' => true,
            'group_id' => 1,
            'action_id' => 1,
            'demo' => true,
        ]);
        $group = Group::create([
            'name' => 'Demo-Gruppe',
            'demo' => true,
            'global' => false,
            'user_id' => $user['id'],
        ]);
        $user->update(['group_id' => $group['id']]);
        GroupUser::create([
            'group_id' => $group['id'],
            'user_id' => $user['id'],
            'role_id' => config('status.role_groupleader'),
        ]);
        GroupUser::create([
            'group_id' => 1,
            'user_id' => $user['id'],
            'role_id' => config('status.role_leader'),
        ]);
        $center = Address::factory()->create([
            'name' => 'Demo-Aktion',
            'firstname' => '',
            'group_id' => $group['id'],
            'center' => true,
        ]);
        $action = Action::create([
            'name' => 'Demo-Aktion',
            'year' => '2023',
            'group_id' => $group['id'],
            'action_status_id' => config('status.action_aktiv'),
            'address_id' => $center['id'],
            'APIKey' => config('geocoder.key'),
            'demo' => true,
            'global' => false,
            'user_id' => $user['id'],
        ]);
        $user->update(['action_id' => $action['id']]);
        ActionUser::create([
            'action_id' => $action['id'],
            'user_id' => $user['id'],
            'role_id' => config('status.role_actionleader'),
        ]);
        ActionUser::create([
            'action_id' => 1,
            'user_id' => $user['id'],
            'role_id' => config('status.role_leader'),
        ]);
        for ($i = 0; $i < 4; $i++) {
            $name = 'leiter'.($i + 1).'@demo';
            $leader[] = User::factory()->create([
                'username' => $name,
                'email' => $name,
                'slug' => $name,
                'password' => Hash::make($name),
                'role_id' => config('status.role_leader'),
                'is_active' => true,
                'group_id' => $group['id'],
                'action_id' => $action['id'],
                'demo' => true,
            ]);
            GroupUser::create([
                'group_id' => $group['id'],
                'user_id' => $leader[$i]['id'],
                'role_id' => config('status.role_leader'),
            ]);
            GroupUser::create([
                'group_id' => 1,
                'user_id' => $leader[$i]['id'],
                'role_id' => config('status.role_leader'),
            ]);
            ActionUser::create([
                'action_id' => $action['id'],
                'user_id' => $leader[$i]['id'],
                'role_id' => config('status.role_leader'),
            ]);
            ActionUser::create([
                'action_id' => 1,
                'user_id' => $leader[$i]['id'],
                'role_id' => config('status.role_leader'),
            ]);
            for ($j = 0; $j < 4; $j++) {
                $status = ($j + 1) * 5;
                $route = Route::create([
                    'name' => 'Route_'.($i + 1).($j + 1).'@demo',
                    'action_id' => $action['id'],
                    'user_id' => $leader[$i]['id'],
                    'route_status_id' => $status,
                    'route_type_id' => min(($j + 1) * 5, 15),
                ]);
                for ($k = 0; $k < 6; $k++) {
                    $address = Address::factory()->create([
                        'group_id' => $group['id'],
                    ]);
                    if ($status < 15) {
                        $order_status = 5;
                    } else {
                        $order_status = $status;
                    }
                    Order::create([
                        'quantity' => fake()->numberBetween(1, 5),
                        'route_id' => $route['id'],
                        'action_id' => $action['id'],
                        'address_id' => $address['id'],
                        'order_status_id' => $order_status,
                        'comments' => fake()->sentence(),
                    ]);
                }
            }
        }
        for ($k = 0; $k < 15; $k++) {
            BakeryProgress::create([
                'action_id' => $action['id'],
                'user_id' => $user['id'],
                'when' => date('H:i', (strtotime('07:00') + 1800 * $k)),
                'raw_material' => fake()->randomNumber(1),
                'dough' => fake()->randomNumber(2),
                'braided' => fake()->randomNumber(2),
                'baked' => fake()->randomNumber(2),
                'delivered' => fake()->randomNumber(2),
                'total' => fake()->randomNumber(3),
            ]);
        }
        for ($k = 0; $k < 40; $k++) {
            $input['quantity'] = fake()->numberBetween(1, 3);
            $user = $leader[fake()->numberBetween(0, 3)];
            $input['user'] = $user['username'];
            $input['text'] = fake()->sentence();
            $input['when'] = fake()->time();
            NotificationCreate::dispatch($action, $input);
        }
    }
}
