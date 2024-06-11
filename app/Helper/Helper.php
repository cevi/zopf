<?php

namespace App\Helper;

use App\Models\User;
use App\Models\Group;
use App\Models\Route;
use App\Models\Action;
use App\Models\Address;
use App\Models\GroupUser;
use App\Models\ActionUser;
use Illuminate\Support\Str;
use Spatie\Geocoder\Geocoder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class Helper
{
    public static function CreateRouteSequence($route)
    {
        $action = $route->action;
        $response = '';
        if (!$action->demo) {
            $orders = $route->orders;
            $center = $action->center;
            $key = $action['APIKey'];

            $url = 'directions/json?origin=' . $center['lat'] . ',' . $center['lng'];
            $url = $url . '&destination=' . $center['lat'] . ',' . $center['lng'];
            $url = $url . '&mode=' . strtolower($route->route_type['travelmode']);
            $url = $url . '&waypoints=optimize:true|';
            foreach ($orders as $order) {
                $address = Address::findOrFail($order['address_id']);
                $url = $url . $address['lat'] . ',' . $address['lng'] . '|';
            }
            $url = rtrim($url, '| ');
            $client = new \GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/maps/api/']);
            $request = $client->get($url . '&key=' . $key);
            $response = json_decode($request->getBody(), true);
            if (!$route['sequenceDone'] && ($response['status'] === 'OK')) {
                foreach ($response['routes'][0]['waypoint_order'] as $key => $waypoint) {
                    $orders[$waypoint]->update(['sequence' => $key]);
                }
                $route->update(['sequenceDone' => true]);
            }

            return $response;
        }
    }

    public static function updateGroup(User $user, Group $group, $fromUser = false)
    {
        $group_user = GroupUser::firstOrCreate(['group_id' => $group->id, 'user_id' => $user->id]);
        if($fromUser){
            $role_id = $user->role ? $user->role->id : config('status.role_leader');
        }
        else{
            $role_id = $group_user->role ? $group_user->role->id : config('status.role_leader');
        }
        $group_user->update(['role_id' => $role_id]);
        $user->update([
            'group_id' => $group->id,
            'role_id' => $role_id,
        ]);
        if ($user->action) {
            if ($user->action->group['id'] != $group->id) {
                $user->update(['action_id' => null]);
            }
        }
    }

    public static function updateAction(User $user, Action $action, $fromUser = false)
    {
        $action_user = ActionUser::firstOrCreate(['action_id' => $action->id, 'user_id' => $user->id]);
        if($fromUser){
            $role_id = $user->role ? $user->role->id : config('status.role_leader');
        }
        else{
            $role_id = $action_user->role ? $action_user->role->id : config('status.role_leader');
        }
        $action_user->update(['role_id' => $role_id]);
        $user->update([
            'action_id' => $action->id,
            'role_id' => $role_id,
        ]);
    }

    public static function getGeocoder(string $key): Geocoder
    {
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey($key);
        $geocoder->setCountry('CH');

        return $geocoder;
    }

    public static function GetTimeChartOptions(): array
    {
        return [
            'fill' => true,
            'lineTension' => 0.3,
            'backgroundColor' => '#a8d0f0',
            'borderColor' => '#4f92c7',
            'borderCapStyle' => 'butt',
            'borderDash' => [],
            'borderDashOffset' => 0.0,
            'borderJoinStyle' => 'miter',
            'borderWidth' => 1,
            'pointBorderColor' => '#4f92c7',
            'pointBackgroundColor' => '#fff',
            'pointBorderWidth' => 1,
            'pointHoverRadius' => 5,
            'pointHoverBorderColor' => 'rgba(220,220,220,1)',
            'pointHoverBorderWidth' => 2,
            'pointRadius' => 1,
            'pointHitRadius' => 10,
            'spanGaps' => false,
        ];
    }

    public static function GetTimeChartData(Action $action, $graph_time = false)
    {

        $graphs = $action->notifications()->select(
            DB::raw('sum(quantity) as total'),
            DB::raw("CASE
                    WHEN MOD(TIMESTAMPDIFF(MINUTE, '2020-01-01 00:00:00', `when`) / 30, 1) >= 0.5
                        THEN date_format(DATE_ADD('2020-01-01 00:00:00', Interval CEILING(TIMESTAMPDIFF(MINUTE, '2020-01-01 00:00:00', `when`) / 30) * 30 minute), '%H:%i')
                    ELSE date_format(DATE_ADD('2020-01-01 00:00:00', Interval FLOOR(TIMESTAMPDIFF(MINUTE, '2020-01-01 00:00:00', `when`) / 30) * 30 minute), '%H:%i')
                    END AS time_frame"),
        )
            ->groupBy('time_frame')
            ->orderBy('time_frame', 'ASC')->get();

        if (count($graphs) > 0) {
            $graphs_time_min = strtotime($graphs->first()->time_frame);
            $graphs_time_max = strtotime($graphs->last()->time_frame);
            $diff = ($graphs_time_max - $graphs_time_min) / 1800;

            $graphs_sum[] = $graphs->first()->total;
            $graphs_time[] = $graphs->first()->time_frame;
            $index = 1;

            for ($i = 1; $i <= $diff; $i++) {
                $graphs_time[] = date('H:i', (strtotime($graphs_time[$i - 1]) + 1800));
                $total = 0;
                if (strtotime($graphs[$index]->time_frame) === strtotime($graphs_time[$i])) {
                    $total = $graphs[$index]->total;
                    $index++;
                }
                $graphs_sum[] = $total;
            }
        } else {
            $graphs_time[] = 0;
            $graphs_sum[] = 0;
        }

        return $graph_time ? $graphs_time : $graphs_sum;
    }

    public static function GetZopfChartData(Action $action): array
    {
        $data['cut'] = 0;
        $data['orders_count'] = 0;
        $data['orders_open'] = 0;
        $data['orders_delivery'] = 0;
        $data['orders_open_pickup'] = 0;
        $data['orders_finished'] = 0;
        $data['orders_count_finished'] = 0;
        $data['total'] = 0;
        $data['routes_count'] = 0;
        $data['routes_finished_count'] = 0;

        if ($action) {

            $data['cut'] = $action->notifications()->where('cut', true)->get(['quantity'])->sum('quantity');

            $data['orders_count'] = count($action->orders);
            $data['orders_open'] = $action->orders->where('order_status_id', config('status.order_offen'))->where('pick_up', false)->sum('quantity');
            $data['orders_delivery'] = $action->orders->where('order_status_id', config('status.order_unterwegs'))->where('pick_up', false)->sum('quantity');
            $data['orders_open_pickup'] = $action->orders->where('order_status_id', '<', config('status.order_ausgeliefert'))->where('pick_up', true)->sum('quantity');
            $data['orders_finished'] = $action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert'))->sum('quantity');
            $data['orders_count_finished'] = count($action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert')));
            $data['total'] = $action->orders->sum('quantity') + $data['cut'];
            $routes = Route::where('action_id', $action['id'])->get();
            $data['routes_count'] = count($routes);
            $data['routes_finished_count'] = count(Route::where('action_id', $action['id'])->where('route_status_id', '=', config('status.route_abgeschlossen'))->get());
        }

        return $data;
    }

    public static function GetIconArray($data): \Illuminate\Support\Collection
    {
        return collect([
            (object) [
                'id' => 'overview-breads',
                'icon' => 'fa-bread-slice',
                'name' => 'Zöpfe',
                'number' => $data['orders_finished'] + $data['cut'] . ' / ' . $data['total'],
            ],
            (object) [
                'id' => 'overview-orders',
                'icon' => 'fa-newspaper',
                'name' => 'Bestellungen',
                'number' => $data['orders_count_finished'] . ' / ' . $data['orders_count'],
            ],
            (object) [
                'id' => 'overview-routes',
                'icon' => 'fa-route',
                'name' => 'Routen',
                'number' => $data['routes_finished_count'] . ' / ' . $data['routes_count'],
            ],
            (object) [
                'id' => 'overview-open',
                'icon' => 'fa-house-chimney',
                'name' => 'Offen',
                'number' => $data['orders_open'],
            ],
            (object) [
                'id' => 'overview-delivery',
                'icon' => 'fa-paper-plane',
                'name' => 'Unterwegs',
                'number' => $data['orders_delivery'],
            ],
            (object) [
                'id' => 'overview-pickup',
                'icon' => 'fa-people-carry-box',
                'name' => 'Abholen',
                'number' => $data['orders_open_pickup'],
            ],
        ]);
    }

    public static function AddGroupUsersToAction(Group $group, Action $action): void
    {
        $users = $group->allUsers;
        foreach ($users as $user) {
            ActionUser::FirstOrcreate(
                [
                    'user_id' => $user->id,
                    'action_id' => $action->id,
                ],
                ['role_id' => config('status.role_leader')]
            );
        }
    }

    public static function deleteAction(Action $action)
    {
        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            if($action['amount']===null){
                $action->delete();
            }
            $action_global = Action::where('global',true)->first();
            
            $users = $action->allUsers;
            foreach($users as $user){
                if($action===$user->action()){
                    Helper::updateAction($user, $action_global);
                }
                ActionUser::where('action_id', $action->id)->where('user_id', $user->id)->delete();
            }
        }
    }

    public static function updateAvatar($request, $user){
        if ($file = $request->file('avatar')) {
            $input = $request->all();
            if ($input['cropped_photo_id']) {
                $save_path = 'profiles/'.$user['id'];
                $directory = storage_path('app/public/'.$save_path);
                File::deleteDirectory($directory);
                File::makeDirectory($directory, 0775, true);
                $name =  Str::uuid() . '_' . str_replace(' ', '', $file->getClientOriginalName());
                Image::make($input['cropped_photo_id'])->save($directory.'/'.$name, 80);
                $input['avatar'] = $save_path.'/'.$name;
                $user->update(['avatar' => $input['avatar']]);
            }
        }
    }

    public static function getAvatarPath($avatar)
    {
        $path = null;
        if($avatar){
            if(str_starts_with($avatar, 'https')){
                $path = $avatar;
            }
            else{
                $path = asset("storage/".$avatar);
            }
        }
        return $path;
    }
}
