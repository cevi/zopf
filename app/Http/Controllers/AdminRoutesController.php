<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Helper\Helper;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Route;
use App\Models\RouteStatus;
use App\Models\RouteType;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class AdminRoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Routen';

        return view('admin.routes.index', compact('title'));
    }

    public function createDataTables()
    {
        if (! Auth::user()->isAdmin()) {
            $action = Auth::user()->action;
            if ($action) {
                $routes = Route::where('action_id', $action['id'])->get();
            } else {
                $routes = null;
            }
        } else {
            $routes = Route::all();
        }

        if ($routes) {
            return DataTables::of($routes)
                ->addColumn('status', function ($routes) {
                    return $routes->route_status ? $routes->route_status['name'] : '';
                })
                ->addColumn('user', function ($routes) {
                    return $routes->user ? $routes->user['username'] : '';
                })
                ->addColumn('routetype', function ($routes) {
                    return $routes->route_type ? $routes->route_type['name'] : '';
                })
                ->addColumn('zopf_count', function ($routes) {
                    return $routes->zopf_count();
                })
                ->addColumn('order_count', function ($routes) {
                    return $routes->order_count();
                })
                ->addColumn('Actions', function ($routes) {
                    $buttons = '<form action="'.\URL::route('routes.send', $routes->id).'" method="post">'.csrf_field();
                    if ($routes->route_status['id'] < 10) {
                        $buttons .= ' <a href='.\URL::route('routes.edit', $routes->id).' type="button" class="btn btn-primary btn-sm">Bearbeiten</a>';
                    }
                    $buttons .= ' <a href='.\URL::route('routes.overview', $routes->id).' type="button" class="btn btn-info btn-sm">Übersicht</a>';
                    if (($routes->route_status['id'] == config('status.route_geplant'))) {
                        $buttons .= ' <button type="submit" class="btn btn-secondary btn-sm">Vorbereitet</button>';
                        // <button data-remote='.\URL::route('routes.send', $routes->id).' id="send" class="btn btn-secondary btn-sm">Vorbereitet</button>';
                    }
                    if ($routes->route_status['id'] == config('status.route_vorbereitet')) {
                        $buttons .= ' <button type="submit" class="btn btn-secondary btn-sm">Lossenden</button>';
                    }
                    $buttons .= '</form>';

                    return $buttons;
                })
                ->rawColumns(['Actions'])
                ->make(true);
        } else {
            return [];
        }
    }

    public function createModalDataTables()
    {
        $action = Auth::user()->action;
        $orders = Order::where([
            ['action_id', $action['id']],
            ['order_status_id', config('status.order_offen')],
            ['pick_up', false],
            ['route_id', null], ])->get();
        if ($orders) {
            return DataTables::of($orders)
                ->addColumn('name', function ($orders) {
                    return $orders->address ? $orders->address['name'] : '';
                })
                ->addColumn('firstname', function ($orders) {
                    return $orders->address ? $orders->address['firstname'] : '';
                })
                ->addColumn('street', function ($orders) {
                    return $orders->address ? $orders->address['street'] : '';
                })
                ->addColumn('city', function ($orders) {
                    return $orders->address ? $orders->address['city'] : '';
                })
                ->addColumn('plz', function ($orders) {
                    return $orders->address ? $orders->address['plz'] : '';
                })
                ->addColumn('checkbox', function ($orders) {
                    return '<input type="checkbox" name="checkbox[]" value="'.$orders->id.'" onclick="CheckboxClick(this)"/>';
                })
                ->rawColumns(['checkbox'])
                ->make(true);
        } else {
            return [];
        }
    }

    public function overview($id)
    {
        //
        $action = Auth::user()->action;
        $route = Route::findOrFail($id);
        $center = $action->center;
        $orders = $route->orders;
        $routetype = $route->route_type;
        $key = $action['APIKey'];
        $title = 'Route Übersicht';

        return view('admin.routes.overview', compact('route', 'orders', 'center', 'routetype', 'key', 'title'));
    }

    public function downloadPDF($id)
    {
        $group = Auth::user()->group;
        $action = Auth::user()->action;
        $route = Route::findOrFail($id);
        $center = $action->center;
        $orders = $route->orders;
        if ($route['photo'] === null) {
            $key = $action['APIKey'];

            $response = Helper::CreateRouteSequence($route);
            $path = $response['routes'][0]['overview_polyline']['points'];
            $url = 'https://maps.googleapis.com/maps/api/staticmap?size=512x512&scale=1&maptype=roadmap&mode='.strtolower($route->route_type['travelmode']).'&';

            foreach ($orders as $order) {
                $address = Address::findOrFail($order['address_id']);
                $url = $url.'&markers=color:red%7C'.$address['lat'].','.$address['lng'];
            }
            $url = $url.'&path=enc:'.$path;
            $url = $url.'&key='.$key;
            $image = file_get_contents($url);
            $folder = 'images/'.$group['name'].'/'.$action['name'].'_'.$action['year'].'/';
            if (! Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder, 0775, true, true);
            }
            $path = $folder.$route['name'].'.png';
            Storage::disk('public')->put($path, $image);
            $route->update(['photo' => $path]);
        } else {
            $path = $route['photo'];
        }
        $routetype = $route->route_type;

        $orders = $orders->sortBy('sequence');
//        return view('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'path'));
        $pdf = PDF::loadView('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'path'));

        //return $pdf->download($route['name'].'.pdf');
        return redirect()->back();
    }

    public function map()
    {
        $action = Auth::user()->action;
        $orders = Order::where('action_id', $action['id'])->with('address')->get();
        $routes = Route::where('action_id', $action['id'])->get();
        $routes = $routes->pluck('name')->all();
        $statuses = OrderStatus::pluck('name')->all();
        $center = $action->center;
        $key = $action['APIKey'];
        $title = 'Routenkarte';

        return view('admin.routes.map', compact('orders', 'routes', 'statuses', 'center', 'key', 'title'));
    }

    public function mapfilter(Request $request)
    {
        $action = Auth::user()->action;
        $route = $request->route;
        $status = $request->status;
        $orders = Order::where('action_id', $action['id']);
        if (isset($route) and $route != 'Alle') {
            if ($route != 'Keine') {
                $route = Route::where('name', $route)->first();
                $orders = $orders->where('route_id', $route['id']);
            } else {
                $orders = $orders->whereNull('route_id')->where('pick_up', false);
            }
        }

        if (isset($status) and $status != 'Alle') {
            $order_status = OrderStatus::where('name', $status)->first();
            $orders->where('order_status_id', $order_status['id']);
        }

        $orders = $orders->with('address')->get();

        return $orders;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = 'Route erfassen';
        $group = Auth::user()->group;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username', 'id')->all();
        $route_types = RouteType::pluck('name', 'id')->all();

        return view('admin.routes.create', compact('users', 'route_types', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $action = Auth::user()->action;
        $input['name'] = $request->name;
        $input['action_id'] = $action['id'];
        $input['route_status_id'] = config('status.route_geplant');
        if ($request->route_type_id == null) {
            $input['route_type_id'] = config('status.route_type_driving');
        } else {
            $input['route_type_id'] = $request->route_type_id;
        }
        if ($request->user_id == null) {
            $input['user_id'] = Auth::user()->id;
        } else {
            $input['user_id'] = $request->user_id;
        }
        $route = Route::create($input);

        return redirect()->to('/admin/routes/'.$route['id'].'/edit');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $route = Route::findOrFail($id);
        $group = Auth::user()->group;
        $action = Auth::user()->action;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username', 'id')->all();
        $route_statuses = RouteStatus::pluck('name', 'id')->all();
        $route_types = RouteType::pluck('name', 'id')->all();
        $orders = $route->orders;
        $title = 'Route Bearbeiten';

        $open_orders = Order::where([
            ['action_id', $action['id']],
            ['order_status_id', config('status.order_offen')],
            ['pick_up', false],
            ['route_id', null], ])
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->orderBy('plz')->orderBy('street')->orderBy('name')
            ->with('address')
            ->get('orders.*');

        $center = $action->center;
        $key = $action['APIKey'];

        return view('admin.routes.edit', compact('route', 'users', 'route_statuses', 'route_types', 'orders', 'open_orders', 'center', 'key', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $route = Route::findOrFail($id);
        $route->update($request->all());

        return redirect('/admin/routes');
    }

    public function AssignOrders(Request $request)
    {
        //
        $orders = Order::WhereIn('id', $request['id']);
        $orders->update(['route_id' => $request['route_id']]);

        return redirect('/admin/routes/edit');
    }

    public function RemoveOrder(Request $Request)
    {
        //
        $order = Order::findOrFail($Request['order_id']);
        $order->update(['route_id' => null]);

        return redirect()->to('/admin/routes/'.$Request['route_id'].'/edit');
    }

    public function send($id)
    {
        //
        $route = Route::findOrFail($id);
        if ($route->route_status['id'] === config('status.route_geplant')) {
            $route->update(['route_status_id' => config('status.route_vorbereitet')]);
        } else {
            $action = Auth::user()->action;
            $log['text']  = 'Route '.$route['name'].' wurde gestartet.';
            $log['user'] = $route->user->username;
            Helper::CreateRouteSequence($route);
            NotificationCreate::dispatch($action, $log);
            $orders = $route->orders();
            $orders->update(['order_status_id' => config('status.order_unterwegs')]);
            $route->update(['route_status_id' => config('status.route_unterwegs')]);
        }

        return redirect('/admin/routes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Route::findOrFail($id)->delete();

        return redirect('/admin/routes');
    }
}
