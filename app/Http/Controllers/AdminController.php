<?php

namespace App\Http\Controllers;

use App\Charts\TimeChart;
use App\Charts\ZopfChart;
use App\Helper\Helper;
use App\Models\Logbook;
use App\Models\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function index(){
        $action = Auth::user()->action;
        $title = 'Dashboard';
        if($action){
            $orders_count = count($action->orders);
            $orders_count_open =  count($action->orders->where('order_status_id', config('status.order_offen')));
            $orders_delivery = $action->orders->where('order_status_id', config('status.order_unterwegs'))->where('pick_up',false)->sum('quantity');
            $orders_open =  $action->orders->where('order_status_id', config('status.order_offen'))->where('pick_up',false)->sum('quantity');
            $orders_open_pickup =  $action->orders->where('order_status_id', '<', config('status.order_ausgeliefert'))->where('pick_up',true)->sum('quantity');
            $orders_finished =  $action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert'))->sum('quantity');
            $orders_count_finished =   count($action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert')));

            $logbooks = Logbook::where('action_id', $action['id']);
            $cut = clone $logbooks;
            $graphs = clone $logbooks;
            $cut = $cut->where('cut', true)->sum('quantity');
            $logbooks =$logbooks->get()->sortByDesc('wann');

            $graphs = $graphs->where('quantity', '>',0)->get()->sortBy('wann');
            if(count($graphs)>0){
                $graphs_time_min = $graphs->first()->wann;
                $graphs_time_max = $graphs->last()->wann;
                $graphs_time_max = date('H:i:s', (ceil(strtotime($graphs_time_max)/1800)*1800));

                $diff = ceil((strtotime($graphs_time_max)-strtotime($graphs_time_min))/1800);

                $graphs_time = array();
                $graphs_sum = array();

                array_push($graphs_time, date('H:i:s', (floor(strtotime($graphs_time_min)/1800)*1800)));

                for($i = 0;$i <= $diff; $i++){
                    if($i > 0){
                        array_push($graphs_time, date('H:i:s', strtotime($graphs_time[$i - 1])+1800));
                    }
                    $graph_sum = Logbook::where('action_id', $action['id'])->whereTime('wann', '>', date('H:i:s', strtotime($graphs_time[$i])-900))
                        ->whereTime('wann', '<', date('H:i:s', strtotime($graphs_time[$i])+900));
                    array_push($graphs_sum, $graph_sum->sum('quantity'));
                }
            }
            else{
                $graphs_time[] = 0;
                $graphs_sum[] = 0;
            }
            $total = $action->orders->sum('quantity') + $cut;

            $routes = Route::where('action_id', $action['id'])->get();
            $routes_count = count($routes);

            $open_routes = Route::where('action_id', $action['id'])->where('route_status_id', '<=', config('status.route_unterwegs'))->get()->sortByDesc('route_status_id');

            $routes_finished_count = count(Route::where('action_id', $action['id'])->where('route_status_id', '=', config('status.route_abgeschlossen'))->get());

            $group = Auth::user()->group;
            $users = User::where('group_id', $group['id'])->get();
            $users = $users->pluck('username','id')->all();
        }
        else{
            $total = 0;
            $orders_count = 0;
            $orders_count_open = 0;
            $routes_count = 0;
            $orders_open = 0;
            $orders_delivery = 0;
            $orders_open_pickup = 0;
            $orders_finished = 0;
            $cut = 0;
            $logbooks = 0;
            $open_routes = [];
            $users = 0;
            $graphs_time = 0;
            $graphs_sum = 0;
            $routes_finished_count = 0;
        }

        $icon_array = collect([
            (object) [
                'icon' => 'icon-padnote',
                'name' => 'Zöpfe',
                'number' => $orders_finished + $cut . ' / ' . $total
            ],
            (object) [
                'icon' => 'icon-website',
                'name' => 'Bestellungen',
                'number' => $orders_count_finished . ' / ' . $orders_count
            ],
            (object) [
                'icon' => 'icon-line-chart',
                'name' => 'Routen',
                'number' => $routes_finished_count . ' / ' . $routes_count
            ],
            (object) [
                'icon' => 'icon-home',
                'name' => 'Offen',
                'number' => $orders_open
            ],
            (object) [
                'icon' => 'icon-paper-airplane',
                'name' => 'Unterwegs',
                'number' => $orders_delivery
            ],
            (object) [
                'icon' => 'icon-user',
                'name' => 'Abholen',
                'number' => $orders_open_pickup
            ],
        ]);

        $zopfChart = new ZopfChart;
        $zopfChart->minimalist(true);
        $zopfChart->labels(["Offen", "Unterwegs", "Abholen", "Aufgeschnitten", "Abgeschlossen"]);
        $zopfChart->dataset('Zöpfe','doughnut', [$orders_open, $orders_delivery, $orders_open_pickup, $cut, $orders_finished])
            ->color([ '#4f92c7',"#21d19f","#522a27","#c73e1d","#c59849"])
            ->backgroundColor([ '#4f92c7',"#21d19f","#522a27","#c73e1d","#c59849"]);

        $timeChart = new TimeChart();
        $timeChart->minimalist(true);
        $timeChart->labels($graphs_time);
        $timeChart->dataset('Anzahl Zöpfe', 'line', $graphs_sum)
            ->color(['#4f92c7'])
            ->backgroundColor(['#a8d0f0']);

        return view('admin/index', compact('icon_array','logbooks', 'open_routes', 'users', 'title','timeChart', 'zopfChart'));
    }

    public function logcreate(Request $request){
        $action = Auth::user()->action;
        if($action){
            $input = $request->all();
            if(!$input['user_id']){
                $input['user_id'] = Auth::user()->id;
            }
            if(!$input['comments'] && $input['cut'] > 0){
                $text = $input['cut'] === '1' ? ' Zopf wurde ' : ' Zöpfe wurden ';
                $input['comments'] = $input['cut'] . $text . 'aufgeschnitten.' ;
                $input['quantity'] = $input['cut'];
                $input['cut'] = true;
            }
            else{
                $input['cut'] = false;
                $input['quantity'] = 0;
            }
            Helper::CreateLogEntry($input['user_id'], $action['id'], $input['comments'], $input['wann'], $input['quantity'], $input['cut']);
        }

        return redirect('admin/');
    }
}
