<?php

namespace App\Helper;

use App\Address;
use App\Logbook;

class Helper
{
    static function CreateLogEntry($user_id, $action_id,  $comments, $wann, $quantity = 0,  $cut = false)
    {
        Logbook::create(['user_id' => $user_id, 'action_id' =>$action_id, 'comments' =>  $comments, 'wann' => $wann, 'quantity' => $quantity,  'cut' => $cut]);
    }

    static function CreateRouteSequence($route)
    {
        $action = $route->action;
        $orders = $route->orders;
        $center = $action->center;
        $key = $action['APIKey'];

        $url = 'directions/json?origin=' . $center['lat'] . ',' . $center['lng'];
        $url = $url . '&destination=' . $center['lat'] . ',' . $center['lng'];
        $url = $url . '&mode='. $route->route_type['travelmode'];
        $url = $url . '&waypoints=optimize:true|';
        foreach ($orders as $order){
            $address = Address::findOrFail($order['address_id']);
            $url = $url . $address['lat'] . ',' . $address['lng'] . '|';
        }
        $url = rtrim($url, "| ");
        return $url;
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/maps/api/']);
        $request = $client->get($url . '&key='. $key);
        $response = json_decode($request->getBody(), true);
        foreach ($orders as $key=>$order){
            $order->update(['sequence' => $response['routes'][0]['waypoint_order'][$key]]);
        }
        return $response;
    }
}