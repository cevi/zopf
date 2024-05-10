<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Models\Help;
use App\Models\Order;
use App\Models\Route;
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
        $group = Auth::user()->group;
        $action = Auth::user()->action;
        $routes = [];
        if ($action) {
            $routes = Route::where('user_id', $user->id)->where('action_id', $action['id'])->where('route_status_id', config('status.route_unterwegs'))->get();
        }
        $title = 'Routenübersicht';
        $help = Help::where('title', $title)->first();

        return view('home', compact('user', 'group', 'routes', 'action', 'title', 'help'));
    }

    public function routes($id)
    {
        $user = Auth::user();
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get();
        $route = Route::FindOrFail($id);
        $orders = $route->orders;
        $title = $route['name'];
        $help = Help::where('title', 'Mobile Routen')->first();

        return view('home.main', compact('route', 'orders', 'routes', 'title', 'help'));
    }

    public function maps($id)
    {
        $user = Auth::user();
        $action = Auth::user()->action;
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get();
        $route = Route::FindOrFail($id);
        $orders = Order::where('route_id', $route['id']);
        $orders = $orders->with('address')->get();
        $center = $action->center;
        $key = $action['APIKey'];
        $title = $route['name'];
        $help = Help::where('title', 'Mobile Karten')->first();

        return view('home.map', compact('orders', 'route', 'routes', 'center', 'key', 'title', 'help'));
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

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $order = Order::findOrFail($id);
            $action = $aktUser->action;
            $route_id = $order['route_id'];
            if ($order['quantity'] === 1) {
                $text = 'Ein Zopf wurde';
            } else {
                $text = $order['quantity'].' Zöpfe wurden';
            }
            if ($new_status === config('status.order_hinterlegt')) {
                $log['text'] = $text.' bei '.$order->address['firstname'].' '.$order->address['name'].' hinterlegt.';
            } else {
                $log['text'] = $text.' an '.$order->address['firstname'].' '.$order->address['name'].' übergeben.';
            }
            $log['user'] = $aktUser->username;
            $log['quantity'] = $order['quantity'];
            $log['route_id'] = $route_id;
            NotificationCreate::dispatch($action, $log);
            $order->update(['order_status_id' => $new_status]);
            $orders = Order::where('route_id', $route_id);
            if ($orders->min('order_status_id') > config('status.order_unterwegs')) {
                $route = Route::FindOrFail($route_id);
                $log['user'] = Auth::user()->username;
                $log['text'] = 'Route '.$route['name'].' wurde abgeschlossen';
                $log['quantity'] = 0;
                NotificationCreate::dispatch($action, $log);
                $route->update(['route_status_id' => config('status.route_abgeschlossen')]);

                return redirect('/home');
            }
        }

        return back();
    }
}
