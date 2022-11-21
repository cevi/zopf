<?php

namespace App\Helper;

use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Address;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Logbook;
use App\Models\User;
use Spatie\Geocoder\Geocoder;

class Helper
{
    public static function CreateLogEntry($user_id, $action_id, $comments, $wann, $quantity = 0, $cut = false)
    {
        Logbook::create(['user_id' => $user_id, 'action_id' => $action_id, 'comments' => $comments, 'wann' => $wann, 'quantity' => $quantity,  'cut' => $cut]);
    }

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
        if (! $route['sequenceDone']) {
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
}
