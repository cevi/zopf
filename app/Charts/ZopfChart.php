<?php

declare(strict_types = 1);

namespace App\Charts;

use App\Logbook;
use Chartisan\PHP\Chartisan;
use Illuminate\Http\Request;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Support\Facades\Auth;

class ZopfChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $action = Auth::user()->getAction();
        if($action){
            $orders_delivery = $action->orders->where('order_status_id', config('status.order_unterwegs'))->where('pick_up',false)->sum('quantity');
            $orders_open =  $action->orders->where('order_status_id', config('status.order_offen'))->where('pick_up',false)->sum('quantity');
            $orders_open_pickup =  $action->orders->where('order_status_id', '<', config('status.order_ausgeliefert'))->where('pick_up',true)->sum('quantity');
            $orders_finished =  $action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert'))->sum('quantity');
           
            $cut = Logbook::where('action_id', $action['id'])->where('cut', true)->sum('quantity');
        }
        return Chartisan::build()
            ->labels(["Offen", "Unterwegs", "Abholen", "Aufgeschnitten", "Abgeschlossen"])
            ->dataset('Zöpfe', [$orders_open, $orders_delivery, $orders_open_pickup, $cut, $orders_finished]);
    }
}