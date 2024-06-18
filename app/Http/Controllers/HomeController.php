<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\Order;
use App\Models\Route;
use App\Helper\Helper;
use App\Events\NotificationCreate;
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
        return Helper::checkRoute($id, config('status.order_ausgeliefert')) ? redirect('/home') : back();
    }

    public function deposited($id)
    {
        return Helper::checkRoute($id, config('status.order_hinterlegt')) ? redirect('/home') : back();
    }

    
}
