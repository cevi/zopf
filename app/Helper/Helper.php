<?php

namespace App\Helper;

use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Address;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Logbook;
use App\Models\Route;
use App\Models\User;
use PhpParser\Node\Expr\Cast\Bool_;
use Spatie\Geocoder\Geocoder;

class Helper
{

    public static function CreateRouteSequence($route)
    {
        $action = $route->action;
        $orders = $route->orders;
        $center = $action->center;
        $key = $action['APIKey'];

        $url = 'directions/json?origin='.$center['lat'].','.$center['lng'];
        $url = $url.'&destination='.$center['lat'].','.$center['lng'];
        $url = $url.'&mode='.strtolower($route->route_type['travelmode']);
        $url = $url.'&waypoints=optimize:true|';
        foreach ($orders as $order) {
            $address = Address::findOrFail($order['address_id']);
            $url = $url.$address['lat'].','.$address['lng'].'|';
        }
        $url = rtrim($url, '| ');
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/maps/api/']);
        $request = $client->get($url.'&key='.$key);
        $response = json_decode($request->getBody(), true);
        if (!$route['sequenceDone'] && ($response['status']==='OK')) {
            foreach ($response['routes'][0]['waypoint_order'] as $key => $waypoint) {
                $orders[$waypoint]->update(['sequence' => $key]);
            }
            $route->update(['sequenceDone' => true]);
        }

        return $response;
    }

    public static function updateGroup(User $user, Group $group)
    {
        $group_user = GroupUser::firstOrCreate(['group_id' => $group->id, 'user_id' => $user->id]);
        $user->update([
            'group_id' => $group->id,
            'role_id' => $group_user->role->id, ]);
        if ($user->action) {
            if ($user->action->group['id'] != $group->id) {
                $user->update(['action_id' => null]);
            }
        }
    }

    public static function updateAction(User $user, Action $action)
    {
        $action_user = ActionUser::firstOrCreate(['action_id' => $action->id, 'user_id' => $user->id]);
        $user->update([
            'action_id' => $action->id,
            'role_id' => $action_user->role->id, ]);
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

    public static function GetTimeChartData(Action $action, $graph_time = false): array
    {
        $graphs = $action->notifications->sortBy('when');
        if (count($graphs) > 0) {
            $graphs_time_min = $graphs->first()->when;
            $graphs_time_max = $graphs->last()->when;
            $graphs_time_max = date('H:i:s', (ceil(strtotime($graphs_time_max) / 1800) * 1800));
            $diff = ceil((strtotime($graphs_time_max) - strtotime($graphs_time_min)) / 1800);

            $graphs_time = [];
            $graphs_sum = [];

            array_push($graphs_time, date('H:i:s', (floor(strtotime($graphs_time_min) / 1800) * 1800)));

            for ($i = 0; $i <= $diff; $i++) {
                if ($i > 0) {
                    array_push($graphs_time, date('H:i:s', strtotime($graphs_time[$i - 1]) + 1800));
                }
                $graph_sum = $action->notifications->where('when', '>', date('H:i:s', strtotime($graphs_time[$i]) - 900))
                    ->where('when', '<', date('H:i:s', strtotime($graphs_time[$i]) + 900));
                array_push($graphs_sum, $graph_sum->sum('quantity'));
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
        $data['routes_count_finished'] = 0;

        if($action) {

            $notifications = $action->notifications->sortByDesc('when');
            $cut = $notifications->where('cut', true)->sum('quantity');

            $data['orders_count'] = count($action->orders);
            $data['orders_open'] = $action->orders->where('order_status_id', config('status.order_offen'))->where('pick_up', false)->sum('quantity');
            $data['orders_delivery'] = $action->orders->where('order_status_id', config('status.order_unterwegs'))->where('pick_up', false)->sum('quantity');
            $data['orders_open_pickup'] = $action->orders->where('order_status_id', '<', config('status.order_ausgeliefert'))->where('pick_up', true)->sum('quantity');
            $data['orders_finished'] = $action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert'))->sum('quantity');
            $data['orders_count_finished'] = count($action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert')));
            $data['total'] = $action->orders->sum('quantity') + $cut;
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
                'icon' => 'icon-padnote',
                'name' => 'Zöpfe',
                'number' => $data['orders_finished'] + $data['cut'].' / '.$data['total'],
            ],
            (object) [
                'icon' => 'icon-website',
                'name' => 'Bestellungen',
                'number' => $data['orders_count_finished'].' / '.$data['orders_count'],
            ],
            (object) [
                'icon' => 'icon-line-chart',
                'name' => 'Routen',
                'number' => $data['routes_finished_count'].' / '.$data['routes_count'],
            ],
            (object) [
                'icon' => 'icon-home',
                'name' => 'Offen',
                'number' => $data['orders_open'],
            ],
            (object) [
                'icon' => 'icon-paper-airplane',
                'name' => 'Unterwegs',
                'number' => $data['orders_delivery'],
            ],
            (object) [
                'icon' => 'icon-user',
                'name' => 'Abholen',
                'number' => $data['orders_open_pickup'],
            ],
        ]);
    }
}
