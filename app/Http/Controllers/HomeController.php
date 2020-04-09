<?php

namespace App\Http\Controllers;

use App\Route;
use App\Action;
use App\Logbook;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $routes = Route::where('user_id', $user->id)->where('route_status_id', 15)->get();
   
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        return view('home', compact('user','routes', 'action'));
    }

    public function routes($id)
    {
        $user = Auth::user();
        $routes = Route::where('user_id', $user->id)->where('route_status_id', 15)->get(); 
        $route = Route::FindOrFail($id); 
        $orders = $route->orders;

        return view('home.main', compact('route', 'orders', 'routes'));
    }

    public function maps($id)
    {        
        $user = Auth::user();
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',config('status.action_aktiv'))->first();  
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get();  
        $route = Route::FindOrFail($id); 
        $orders = Order::where('route_id',$route['id']);
        $orders = $orders->with('address')->get();
        $center = $action->address;
        return view('home.map', compact('orders', 'route','routes','center'));
    }

    public function delivered($id)
    { 
        return $this->check_route($id, config('status.order_ausgeliefert'));
    }

    public function deposited($id)
    { 
        return $this->check_route($id, config('status.order_hinterlegt')); 
    }

    public function check_route($id, $new_status)
    {    
        $order = Order::findOrFail($id);
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',config('status.action_aktiv'))->first();  
        $route_id = $order['route_id']; 
        if($order['quantity']===1){
            $text = 'Ein Zopf';
        }
        else
        {
            $text = $order['quantity'].' Zöpfe';
        }
        $text = $text.' wurden an '.$order->address['firstname'].' '.$order->address['name'];
        if($new_status===config('status.order_hinterlegt')){
            $text = $text.' hinterlegt.';
        }
        else
        {
            $text = $text.' übergeben.';
        }
        Logbook::create(['user_id' => Auth::user()->id, 'action_id' => $action['id'], 'comments' => $text]);
        $order->update(['order_status_id' => $new_status]);
        $orders = Order::where('route_id',$route_id);
        if($orders->min('order_status_id') > config('status.order_unterwegs')){
            $route = Route::FindOrFail($route_id);
            Logbook::create(['user_id' => Auth::user()->id, 'action_id' => $action['id'], 'comments' => 'Route '.$route['name'].' wurde abgeschlossen']);
            $route->update(['route_status_id' => config('status.route_abgeschlossen')]);
            return redirect('/');
        }
        return back();
    }
}
