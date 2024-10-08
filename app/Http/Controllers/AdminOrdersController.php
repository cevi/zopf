<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Helper\Helper;
use App\Imports\OrdersImport;
use App\Models\Address;
use App\Models\Help;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Route;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Geocoder\Facades\Geocoder;

class AdminOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Bestellungen';
        $action = Auth::user()->action;
        $order_statuses = OrderStatus::pluck('name');
        if ($action) {
            $routes = Route::where('action_id', $action['id'])->where('route_status_id', config('status.route_geplant'))->get();
        } else {
            $routes = null;
        }

        $help = Help::where('title', $title)->first();

        return view('admin.orders.index', compact('routes', 'title', 'order_statuses', 'help'));
    }

    public function createDataTables(Request $request)
    {
        $input = $request->all();
        $order_status = OrderStatus::where('name', '=', $input['status'])->first();
        $pickup = $input['pickup'] === 'Abholen' ? true : null;
        $noRoute = $input['pickup'] === 'Keine Route' ? true : null;
        if (! Auth::user()->isAdmin()) {
            $action = Auth::user()->action;
            if ($action) {
                $orders = Order::where('action_id', $action['id'])
                    ->when($order_status, function ($query, $order_status) {
                        $query->where('order_status_id', '=', $order_status['id']);
                    })
                    ->when($pickup, function ($query, $pickup) {
                        $query->where('pick_up', $pickup);
                    })
                    ->when($noRoute, function ($query, $noRoute) {
                        $query->whereNull('route_id')->where('pick_up', false);
                    })
                    ->get();
            } else {
                $orders = null;
            }
        } else {
            $orders = Order::when($order_status, function ($query, $order_status) {
                $query->where('order_status_id', '=', $order_status['id']);
            })
                ->when($pickup, function ($query, $pickup) {
                    $query->where('pick_up', $pickup);
                })
                ->get();
        }
        if ($orders) {
            return DataTables::of($orders)
                ->addColumn('name', function ($orders) {
                    return $orders->address ? $orders->address['name'] : '';
                })
                ->addColumn('firstname', function ($orders) {
                    $firstname = $orders->address ? $orders->address['firstname'] : '';

                    return '<a href='.\URL::route('orders.edit', $orders).' class="font-medium text-blue-600 dark:text-blue-500 hover:underline">'.$firstname.'</a>';
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
                ->editColumn('route', function ($orders) {
                    $route = $orders->route ? $orders->route['name'] : '';
                    if ($route != '') {
                        if ($orders->route['route_status_id'] > config('status.route_geplant')) {
                            $link = '<a href='.\URL::route('routes.overview', $orders->route).'  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">';
                        } else {
                            $link = '<a href='.\URL::route('routes.edit', $orders->route).'  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">';
                        }
                        $route = $link.$route.'</a>';
                    }

                    return $route;
                })
                ->addColumn('status', function ($orders) {
                    return [
                        'display' => $orders->order_status ? $orders->order_status['name'] : '',
                        'sort' => $orders->order_status['id'],
                    ];
                })
                ->addColumn('pick_up', function ($orders) {
                    return $orders['pick_up'] ? 'Ja' : 'Nein';
                })
                ->addColumn('Actions', function ($orders) {
                    $buttons = '<div class="row"><div class ="col-6">';
                    if ($orders['pick_up'] && ($orders['order_status_id'] == config('status.order_offen'))) {
                        $buttons .= ' <a href="javascript:void(0)"  class="pick-up" title="Abgeholt" data-remote='.\URL::route('orders.pickup', $orders).'><i class="fa-solid fa-people-carry-box fa-xl"></i></a>';
                    }
                    $buttons .= '</form></div><div class ="col-6"> <a href="javascript:void(0)" class="delete" data-remote='.\URL::route('orders.destroy', $orders).'><i style="color:	#FAA0A0" class="fa-solid fa-trash fa-xl"></i></a></div></div>';

                    return $buttons;
                })
                ->addColumn('checkbox', function ($orders) {
                    return '<input type="checkbox" name="checkbox[]" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" value="'.$orders->id.'"/>';
                })
                ->rawColumns(['Actions', 'checkbox', 'route', 'firstname'])
                ->make(true);
        } else {
            return [];
        }
    }

    public function createRoute(Request $request)
    {

        $user = Auth::user();
        if (! $user->demo) {
            $action = $user->action;
            if ($request->name == '') {
                $route = Route::FindOrFail($request->route_id);
            } else {
                $input['name'] = $request->name;
                $input['action_id'] = $action['id'];
                $input['route_status_id'] = config('status.route_geplant');
                $input['user_id'] = Auth::user()->id;
                $route = Route::create($input);
            }
            Order::WhereIn('id', $request->id)->update(['route_id' => $route['id']]);

        }

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pickup(Order $order)
    {
        //
        $action = Auth::user()->action;

        if ($action && ! Auth::user()->demo) {
            $order->update(['order_status_id' => config('status.order_abgeholt')]);
            if ($order['quantity'] === 1) {
                $text = 'Ein Zopf wurde';
            } else {
                $text = $order['quantity'].' Zöpfe wurden';
            }
            $name = $order->address['firstname'];
            $name .= strlen($name) > 0 ? ' ' : '';
            $name .= $order->address['name'];
            $input['text'] = $text.' von '.trim($name).' abgeholt.';
            $input['quantity'] = $order['quantity'];

            NotificationCreate::dispatch($action, $input);
        }

        return redirect('/admin/orders');
    }

    public function create()
    {
        //
        $action = Auth::user()->action;
        $routes = Route::where('route_status_id', config('status.route_geplant'))->where('action_id', $action['id'])->get();
        $routes = $routes->pluck('name', 'id')->all();
        $title = 'Bestellung - Erfassen';

        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Bestellung';
        $help['main_route'] = '/admin/orders';

        return view('admin.orders.create', compact('routes', 'title', 'help'));
    }

    public function map()
    {
        $action = Auth::user()->action;
        $orders = Order::where('action_id', $action['id']);
        $orders = $orders->with('address')->get();
        $cities = $action->addresses->unique('plz')->pluck('city', 'plz');
        $statuses = OrderStatus::pluck('name')->all();
        $center = $action->center;
        $key = $action['APIKey'];
        $title = 'Bestellung - Karte';

        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Bestellung';
        $help['main_route'] = '/admin/orders';

        return view('admin.orders.map', compact('orders', 'cities', 'statuses', 'center', 'key', 'title', 'help'));
    }

    public function mapfilter(Request $request)
    {
        $action = Auth::user()->action;
        $addresses = $action->addresses;
        $city = $request->city;
        $status = $request->status;
        $orders = Order::where('action_id', $action['id']);
        if (isset($city) and $city != 'Alle') {
            $addresses_id = $addresses->where('plz', $city)->pluck('id')->all();
            $orders = $orders->whereIn('address_id', $addresses_id);
        }

        if (isset($status) and $status != 'Alle') {
            $order_status = OrderStatus::where('name', $status)->first();
            $orders->where('order_status_id', $order_status['id']);
        }

        $orders = $orders->with('address')->get();

        return $orders;
    }

    public function uploadFile(Request $request)
    {

        if (! Auth::user()->demo && $request->hasFile('csv_file')) {
            $array = (new OrdersImport)->toArray(request()->file('csv_file'));
            $importData_arr = $array[0];

            // Insert to MySQL database
            $user = Auth::user();
            $group = $user->group;
            $action = $user->action;
            $center = $action->center;
            $geocoder = Helper::getGeocoder($action['APIKey']);
            foreach ($importData_arr as $importData) {
                // return $importData;
                $importData['abholung'] = $importData['abholung'] === 'x' ? 1 : 0;
                $importData = array_map('trim', $importData);
                if ($importData['abholung']) {
                    $insertAddress = [
                        'name' => $importData['name'],
                        'firstname' => $importData['vorname'],
                        'street' => $center['street'],
                        'group_id' => $user->group['id'],
                        'city' => $center['city'],
                        'plz' => $center['plz'],
                        'lat' => $center['lat'],
                        'lng' => $center['lng'], ];
                } else {
                    $insertAddress = [
                        'name' => $importData['name'],
                        'firstname' => $importData['vorname'],
                        'street' => $importData['strasse'],
                        'group_id' => $user->group['id'],
                        'city' => $importData['ortschaft'],
                        'plz' => $importData['plz'], ];
                }

                $address = Address::firstOrCreate(['name' => $importData['name'], 'firstname' => $importData['vorname'], 'group_id' => $group['id']], $insertAddress);

                if ($address) {
                    if (! $importData['abholung']) {
                        $geocode = $geocoder->getCoordinatesForAddress($address['street'].', '.$address['plz'].' '.$address['city']);
                        $lat = $geocode['lat'];
                        $lng = $geocode['lng'];

                        $address->update(['lat' => $lat, 'lng' => $lng]);
                    }

                    if ($importData['route']) {
                        $insertRoute = [
                            'name' => $importData['route'],
                            'action_id' => $user->action,
                            'route_status_id' => config('status.route_geplant'), ];

                        $route = Route::firstOrCreate(['name' => $importData['route']], $insertRoute);
                        $route_id = $route['id'];
                    } else {
                        $route_id = null;
                    }

                    $insertOrder = [

                        'quantity' => $importData['anzahl'],
                        'route_id' => $route_id,
                        'action_id' => $action['id'],
                        'address_id' => $address['id'],
                        'order_status_id' => config('status.order_offen'),
                        'pick_up' => $importData['abholung'],
                        'comments' => $importData['bemerkung'], ];
                }
                Order::firstOrCreate(['action_id' => $action['id'], 'address_id' => $address['id'], 'quantity' => $importData['anzahl'], 'comments' => $importData['bemerkung']], $insertOrder);
            }
        }

        return redirect()->action('AdminOrdersController@index');
    }

    // public function searchResponseAddress(Request $request)
    // {
    //     $addresses = Address::search($request->get('term'))->get();
    //     $data=array();
    //     foreach ($addresses as $address) {
    //             $data[]=array('name'=>$address->name,'firstname'=>$address->firstname,
    //                 'street'=> $address->street, 'city_name' => $address->city, 'city_plz' => $address->plz, 'address_id'=>$address->id);
    //     }
    //     if(count($data))
    //          return $data;
    //     else
    //         return ['name'=>'', 'firstname'=>'', 'street'=>'', 'city_name'=>'', 'city_plz'=>'','address_id'=>''];
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        $action = $user->action;

        if ($action && ! Auth::user()->demo) {
            $address = Address::Where('id', $request->address_id)->first();

            if (! $address) {
                $input = $request->all();

                if (! $user->isAdmin()) {
                    $group = $user->group;
                    $input['group_id'] = $group['id'];
                }

                if ($request->has('pick_up')) {
                    $center = $action->center;
                    $input['street'] = $center['street'];
                    $input['plz'] = $center['plz'];
                    $input['city'] = $center['city'];
                    $input['lat'] = $center['lat'];
                    $input['lng'] = $center['lng'];
                    $input['lng'] = $center['lng'];
                    $input['pick_up'] = true;
                } else {
                    $geocoder = Helper::getGeocoder($action['APIKey']);
                    $geocode = $geocoder->getCoordinatesForAddress($input['street'].', '.$input['plz'].' '.$input['city']);
                    $input['lat'] = $geocode['lat'];
                    $input['lng'] = $geocode['lng'];
                }
                $address = Address::create($input);

            // return $input;
            } else {
                $input = $request->all();
            }

            $input['address_id'] = $address->id;
            $input['action_id'] = $action['id'];
            $input['order_status_id'] = config('status.order_offen');

            Order::create($input);
        }

        return redirect('/admin/orders/create');
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
    public function edit(Order $order)
    {
        //
        $title = 'Bestellung - Bearbeiten';
        $action = Auth::user()->action;
        $routes = Route::where('action_id', $action['id'])->get();
        $routes = $routes->pluck('name', 'id')->all();
        $routes = ['' => 'Keine Route'] + $routes;
        $order_statuses = OrderStatus::pluck('name', 'id')->all();

        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Bestellung';
        $help['main_route'] = '/admin/orders';

        return view('admin.orders.edit', compact('order', 'routes', 'title', 'order_statuses', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $order = Order::findOrFail($id);
        $address = $order->address;
        $user = Auth::user();
        $action = $user->action;

        if ($action && ! Auth::user()->demo) {
            GeoCoder::setApiKey($action['APIKey']);
            GeoCoder::setCountry('CH');
            if ($address) {
                $input = $request->all();

                $input['pick_up'] = isset($input['pick_up']);
                $geocode = Geocoder::getCoordinatesForAddress($input['street'].', '.$input['plz'].' '.$input['city']);
                $input['lat'] = $geocode['lat'];
                $input['lng'] = $geocode['lng'];

                $address->update($input);
            } else {
                $input = $request->all();
                if (! Auth::user()->isAdmin()) {
                    $group = Auth::user()->group;
                    $input['group_id'] = $group['id'];
                }
                $geocode = Geocoder::getCoordinatesForAddress($input['street'].', '.$input['plz'].' '.$input['city']);
                $input['lat'] = $geocode['lat'];
                $input['lng'] = $geocode['lng'];

                // return $input;

                $address = Address::create($input);
            }
            $order->update($input);
        }

        return redirect('/admin/orders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //

        if (! Auth::user()->demo) {
            $order->delete();
        }

        return redirect('/admin/orders');
    }
}
