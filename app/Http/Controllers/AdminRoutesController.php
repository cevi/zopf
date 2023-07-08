<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Helper\Helper;
use App\Models\Address;
use App\Models\Help;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Route;
use App\Models\RouteStatus;
use App\Models\RouteType;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use PDF;
use Str;

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
        $help = Help::where('title',$title)->first();

        return view('admin.routes.index', compact('title', 'help'));
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
                    return [
                        'display' => $routes->route_status ? $routes->route_status['name'] : '',
                        'sort' => $routes->route_status['id'],
                    ];
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
                    if ($routes->route_status['id'] < config('status.route_abgeschlossen')) {
                        $buttons .= ' <a href='.\URL::route('routes.edit', $routes->id).' type="button" class="btn btn-primary btn-sm">Bearbeiten</a>';
                    }
                    $buttons .= ' <a href='.\URL::route('routes.overview', $routes->id).' type="button" class="btn btn-info btn-sm">Übersicht</a>';
                    if (($routes->route_status['id'] == config('status.route_geplant')) && $routes['route_type']) {
                        $buttons .= ' <button type="submit" class="btn btn-secondary btn-sm">Vorbereitet</button>';
                    }
                    if ($routes->route_status['id'] == config('status.route_vorbereitet')) {
                        $buttons .= ' <button type="submit" class="btn btn-secondary btn-sm">Lossenden</button>';
                        $buttons .= ' <button type="submit" name="planned" class="btn btn-secondary btn-sm">Geplant</button>';
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
        $title = 'Route - Übersicht';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Routen';
        $help['main_route'] =  '/admin/routes';

        return view('admin.routes.overview', compact('route', 'orders', 'center', 'routetype', 'key', 'title', 'help'));
    }

    public function downloadPDF($id)
    {
        $user = Auth::user();
        if (!$user->demo) {
            $group = $user->group;
            $action = $user->action;
            $route = Route::findOrFail($id);
            $center = $action->center;
            $orders = $route->orders;
            if ($route['photo'] === null) {
                $key = $action['APIKey'];

                $response = Helper::CreateRouteSequence($route);
                $path = $response['routes'][0]['overview_polyline']['points'];
                $url = 'https://maps.googleapis.com/maps/api/staticmap?size=512x512&scale=1&maptype=roadmap&mode=' . strtolower($route->route_type['travelmode']) . '&';

                foreach ($orders as $order) {
                    $address = Address::findOrFail($order['address_id']);
                    $url = $url . '&markers=color:red%7C' . $address['lat'] . ',' . $address['lng'];
                }
                $url = $url . '&path=enc:' . $path;
                $url = $url . '&key=' . $key;
                $image = file_get_contents($url);
                $folder = 'images/' . Str::slug($group['name']) . '/' . Str::slug($action['name']) . '_' . $action['year'] . '/';
                $directory = storage_path('app/public/' . $folder);
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0775, true);
                }
                $path = Str::uuid() . '_' . Str::slug($route['name']) . '.png';
                Image::make($image)->save($directory . '/' . $path, 80);
                $save_path = $folder . $path;
                $route->update(['photo' => $save_path]);
            } else {
                $save_path = $route['photo'];
            }
            $routetype = $route->route_type;

            $orders = $orders->sortBy('sequence');
            return View('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'save_path'));
            $pdf = PDF::loadView('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'save_path'));
            return $pdf->download(Str::slug($route['name']).'.pdf');
        }
        else{
            return redirect()->back();
        }
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
        $title = 'Routen - Karte';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Routen';
        $help['main_route'] =  '/admin/routes';

        return view('admin.routes.map', compact('orders', 'routes', 'statuses', 'center', 'key', 'title', 'help'));
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
        $title = 'Route - Erfassen';
        $group = Auth::user()->group;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username', 'id')->all();
        $route_types = RouteType::pluck('name', 'id')->all();
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Routen';
        $help['main_route'] =  '/admin/routes';

        return view('admin.routes.create', compact('users', 'route_types', 'title', 'help'));
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

        $user = Auth::user();
        if (!$user->demo) {
            $action = $user->action;
            $input['name'] = $request->name;
            $input['action_id'] = $action['id'];
            $input['route_status_id'] = config('status.route_geplant');
            if ($request->route_type_id == null) {
                $input['route_type_id'] = config('status.route_type_driving');
            } else {
                $input['route_type_id'] = $request->route_type_id;
            }
            if ($request->user_id == null) {
                $input['user_id'] = $user->id;
            } else {
                $input['user_id'] = $request->user_id;
            }
            $route = Route::create($input);
        }

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
        $title = 'Route - Bearbeiten';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Routen';
        $help['main_route'] =  '/admin/routes';

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

        return view('admin.routes.edit', compact('route', 'users', 'route_statuses', 'route_types', 'orders', 'open_orders', 'center', 'key', 'title', 'help'));
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

        $user = Auth::user();
        if (!$user->demo) {
            $route = Route::findOrFail($id);
            $route->update($request->all());
        }

        return redirect('/admin/routes');
    }

    public function AssignOrders(Request $request)
    {
        //

        $user = Auth::user();
        if (!$user->demo) {
            $orders = Order::WhereIn('id', $request['id']);
            $orders->update(['route_id' => $request['route_id']]);
        }

        return redirect('/admin/routes/edit');
    }

    public function RemoveOrder(Request $Request)
    {
        //

        $user = Auth::user();
        if (!$user->demo) {
            $order = Order::findOrFail($Request['order_id']);
            $order->update(['route_id' => null]);
        }

        return redirect()->to('/admin/routes/'.$Request['route_id'].'/edit');
    }

    public function send(Request $request, $id)
    {
        //
        $user = Auth::user();
        if (!$user->demo) {
            $route = Route::findOrFail($id);
            if ($route->route_status['id'] === config('status.route_geplant')) {
                $route->update(['route_status_id' => config('status.route_vorbereitet')]);
            }
            elseif($request->has('planned')){
                $route->update(['route_status_id' => config('status.route_geplant')]);
                }
            else{
                $action = $user->action;
                $log['text'] = 'Route ' . $route['name'] . ' wurde gestartet.';
                $log['user'] = $route->user->username;
                Helper::CreateRouteSequence($route);
                NotificationCreate::dispatch($action, $log);
                $orders = $route->orders();
                $orders->update(['order_status_id' => config('status.order_unterwegs')]);
                $route->update(['route_status_id' => config('status.route_unterwegs')]);
            }
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

        $user = Auth::user();
        if (!$user->demo) {
            Route::findOrFail($id)->delete();
        }

        return redirect('/admin/routes');
    }
}
