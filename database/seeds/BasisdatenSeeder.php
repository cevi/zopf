<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\ActionStatus;
use App\Models\Group;
use App\Models\OrderStatus;
use App\Models\Role;
use App\Models\RouteStatus;
use App\Models\RouteType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BasisdatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ActionStatus::create(['id' => config('status.action_geplant'), 'name' => 'Geplant']);
        ActionStatus::create(['id' => config('status.action_aktiv'), 'name' => 'Aktiv']);
        ActionStatus::create(['id' => config('status.action_abgeschlossen'), 'name' => 'Abgeschlossen']);

        OrderStatus::create(['id' => config('status.order_offen'), 'name' => 'Offen']);
        OrderStatus::create(['id' => config('status.order_unterwegs'), 'name' => 'Unterwegs']);
        OrderStatus::create(['id' => config('status.order_ausgeliefert'), 'name' => 'Ausgeliefert']);
        OrderStatus::create(['id' => config('status.order_hinterlegt'), 'name' => 'Hinterlegt']);
        OrderStatus::create(['id' => config('status.order_abgeholt'), 'name' => 'Abgeholt']);

        RouteStatus::create(['id' => config('status.route_geplant'), 'name' => 'Geplant']);
        RouteStatus::create(['id' => config('status.route_vorbereitet'), 'name' => 'Vorbereitet']);
        RouteStatus::create(['id' => config('status.route_unterwegs'), 'name' => 'Unterwegs']);
        RouteStatus::create(['id' => config('status.route_abgeschlossen'), 'name' => 'Abgeschlossen']);

        RouteType::create(['id' => config('status.route_type_walking'), 'name' => 'Zu Fuss', 'travelmode' => 'WALKING']);
        RouteType::create(['id' => config('status.route_type_cycling'), 'name' => 'Fahrrad', 'travelmode' => 'BICYCLING']);
        RouteType::create(['id' => config('status.route_type_driving'), 'name' => 'Auto', 'travelmode' => 'DRIVING']);

        $path = base_path('storage/app/cities.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('City table seeded!');

        Role::create(['id' => config('status.role_administrator'), 'name' => 'Administrator']);
        Role::create(['id' => config('status.role_groupleader'), 'name' => 'Gruppen Chef']);
        Role::create(['id' => config('status.role_actionleader'), 'name' => 'Aktions Chef']);
        Role::create(['id' => config('status.role_leader'), 'name' => 'Leiter']);

        $user = User::create( [
            'id' => config('status.Administrator'),
            'username' => 'Administrator',
            'email' => 'Administrator',
            'is_active' => 1,
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role_id' => config('status.role_administrator'),
            'email_verified_at' => now(),
        ]);

        $group = Group::create([
            'name' => 'Global-Gruppe',
            'global' => true]);

        $action = Action::create([
            'name' => 'Global-Aktion',
            'group_id' => $group['id'],
            'year' => '2022',
            'APIKey' => '',
            'global' => true]);

        $user->update([
            'group_id' => $group['id'],
            'action_id' => $action['id']]);
    }
}
