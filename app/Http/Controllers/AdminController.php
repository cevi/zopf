<?php

namespace App\Http\Controllers;

use App\Order;
use App\Route;
use App\Action;
use App\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function index(){
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->first();
        $order_action = Order::where('action_id', $action['id']);
        $orders = $order_action->get();
        $total = $orders->sum('quantity');
        $orders_count = count($orders);
        $orders_open = $order_action->where('order_status_id', '<', config('status.order_ausgeliefert'));
        $orders_open_delivery = $orders_open;
        $orders_open_pickup = clone $orders_open;
        $orders_open_delivery = $orders_open_delivery->where('pick_up',false)->sum('quantity');
        $orders_open_pickup = $orders_open_pickup->where('pick_up',true)->sum('quantity');
        
        $logbooks = Logbook::where('action_id', $action['id']);
        $cut = clone $logbooks;
        $cut = $cut->where('cut', true)->sum('quantity');
        $logbooks =$logbooks->get()->sortByDesc('created_at');

        $routes = Route::where('action_id', $action['id'])->get();
        $routes_count = count($routes);

        return view('admin/index', compact('total', 'orders_count', 'routes_count', 'orders_open_delivery', 'orders_open_pickup','cut', 'logbooks'));
    }
}
