<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\Route;
use App\Action;
use App\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function index(){
        $action = Auth::user()->getAction();
        if($action){
            $orders_count = count($action->orders);
            $orders_delivery = $action->orders->where('order_status_id', config('status.order_unterwegs'))->where('pick_up',false)->sum('quantity');
            $orders_open =  $action->orders->where('order_status_id', config('status.order_offen'))->where('pick_up',false)->sum('quantity');
            $orders_open_pickup =  $action->orders->where('order_status_id', '<', config('status.order_ausgeliefert'))->where('pick_up',true)->sum('quantity');
            $orders_finished =  $action->orders->where('order_status_id', '>=', config('status.order_ausgeliefert'))->sum('quantity');
           
            $logbooks = Logbook::where('action_id', $action['id']);
            $cut = clone $logbooks;
            $graphs = clone $logbooks;
            $cut = $cut->where('cut', true)->sum('quantity');
            $logbooks =$logbooks->get()->sortByDesc('created_at');


            // $graphs = $graphs->where('quantity', '>',0)->selectRaw('sec_to_time((time_to_sec(time(`wann`)) div 1800)*1800) as halfhour, sum(quantity) as sum')
            // ->groupBy('halfhour')
            // ->get();
            // $graphs_time = array();
            // $graphs_sum = array();
            // foreach($graphs as $graph){
            //     array_push($graphs_time, $graph->halfhour);  
            //     array_push($graphs_sum, $graph->sum);    
            // }
            $graphs = $graphs->where('quantity', '>',0)->get()->sortBy('wann');
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

            $total = $action->orders->sum('quantity') + $cut;

            $routes = Route::where('action_id', $action['id'])->get();
            $routes_count = count($routes);

            $open_routes = Route::where('action_id', $action['id'])->where('route_status_id', config('status.route_unterwegs'))->get();
            
            $group = Auth::user()->group;
            $users = User::where('group_id', $group['id'])->get();
            $users = $users->pluck('username','id')->all();
        }
        else{
            $total = 0;
            $orders_count = 0;
            $orders_open_delivery = 0;
            $orders_on_route = 0;
            $orders_open_pickup = 0;
            $orders_finished = 0;
            $cut = 0;
            $logbooks = 0;
            $routes_count = 0;
            $open_routes = 0;
            $users = 0;
        }

        return view('admin/index', compact('total', 'orders_count', 'routes_count', 'orders_open', 'orders_delivery','orders_open_pickup', 
        'orders_finished', 'cut', 'logbooks', 'open_routes', 'users', 'graphs_time', 'graphs_sum'));
    }

    public function logcreate(Request $request){
        $action = Auth::user()->getAction();
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
            $input['action_id'] = $action['id'];
            Logbook::create($input);
        }

        return redirect('admin/');
    }
}
