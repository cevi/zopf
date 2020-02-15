<?php

namespace App\Http\Controllers;

use App\Order;
use App\Route;
use App\Action;
use App\Address;
use App\OrderStatus;
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
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $routes = Route::where('action_id', $action['id'])->where('route_status_id',5)->get(); 
        return view('admin.orders.index', compact('routes'));
    }

    public function createDataTables()
    {
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
            $orders = Order::where('action_id', $action['id'])->get();

        }
        else{
            $orders = Order::all();
        }

        return DataTables::of($orders)
        ->addColumn('name', function ($orders) {
            return $orders->address['name'];})
        ->addColumn('firstname', function ($orders) {
            return $orders->address['firstname'];})
        ->addColumn('street', function ($orders) {
            return $orders->address['street'];})
        ->addColumn('city', function ($orders) {
            return $orders->address['city'];})
        ->addColumn('plz', function ($orders) {
            return $orders->address['plz'];})
        ->addColumn('route', function ($orders) {
            return $orders->route['name'];})
        ->addColumn('status', function ($orders) {
            return $orders->order_status['name'];})
        ->addColumn('Actions', function($orders) {
            return '<a href='.\URL::route('orders.edit', $orders->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
            <button data-remote='.\URL::route('orders.destroy', $orders->id).' class="btn btn-danger btn-sm">Löschen</button>';
        })
        ->addColumn('checkbox', function ($orders) {
            return '<input type="checkbox" name="checkbox[]" value="'.$orders->id.'"/>';
        })
        ->rawColumns(['Actions', 'checkbox'])
        ->make(true);

    }

    public function createRoute(Request $request)
    {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        if($request->name==''){
            $route = Route::FindOrFail($request->route_id);
        }
        else
        {
            $input['name'] = $request->name;
            $input['action_id'] =  $action['id'];
            $input['route_status_id'] = 5;
            $input['user_id'] = Auth::user()->id;
            $route = Route::create($input);

               
        }
        Order::WhereIn('id',$request->id)->update(['route_id' => $route['id']]);

        $routes = Route::where('action_id', $action['id'])->where('route_status_id',5)->get(); 
        return view('admin.orders.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $routes = Route::where('route_status_id',5)->get();
        $routes = $routes->pluck('name','id')->all();
        return view('admin.orders.create', compact('routes'));
    }

    public function map()
    {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();         
        $orders = Order::where('action_id', $action['id'])->get();
        $cities = $action->addresses->unique('city')->pluck('city');
        $statuses = OrderStatus::pluck('name')->all();
        $center = $action->address;

        return view('admin.orders.map', compact('orders', 'cities', 'statuses', 'center'));
    }

    public function mapfilter(Request $request)
    {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $addresses = $action->addresses;
        $city = $request->city;
        $status = $request->status;
        $orders = Order::where('action_id', $action['id']);
        if(isset($city) and $city!="Alle"){
            $addresses_id = $addresses->where('city',$city)->pluck('id')->all();
            $orders = $orders->whereIn('address_id', $addresses_id);
        }

        if(isset($status) and $status!="Alle"){
            $order_status = OrderStatus::where('name',$status)->first();
            $orders->where('order_status_id',$order_status['id'])->get();
        }
        
        $orders = $orders->with('address')->get();
        return $orders;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $address=Address::Where('id',$request->address_id)->first();
        if(!$address){
            $input = $request->all();
            if(!Auth::user()->isAdmin()){
                $group = Auth::user()->group;
                $input['group_id'] = $group['id'];
            }
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];

            // return $input;

            $address = Address::create($input);
        }
        else{
            $input = $request->all(); 
        }
        $input['address_id'] = $address->id; 
        $action = Auth::user()->getAction();
        $input['action_id'] = $action['id'];
        $input['order_status_id'] = 5;
        
        Order::create($input);

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
    public function edit($id)
    {
        //
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $routes = Route::where('action_id', $action['id'])->where('route_status_id',5)->get(); 
        $routes = $routes->pluck('name','id')->all();
        $order = Order::findOrFail($id);

        return view('admin.orders.edit', compact('order','routes'));
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
        $order = Order::findOrFail($id);
        $address=$order->address;
        if($address){
            $input = $request->all();
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];
            
            $address->update($input);
        }  
        else{
            $input = $request->all();
            if(!Auth::user()->isAdmin()){
                $group = Auth::user()->group;
                $input['group_id'] = $group['id'];
            }
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];

            // return $input;

            $address = Address::create($input);
        }      
        $order->update($input);

        return redirect('/admin/orders');
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
        Order::findOrFail($id)->delete();
        return redirect('/admin/orders');
    }
}
