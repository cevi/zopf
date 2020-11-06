<?php

namespace App\Http\Controllers;

use App\Order;
use App\Route;
use App\Action;
use DataTables;
use App\Address;
use App\OrderStatus;
use Illuminate\Http\Request;
use App\Imports\OrdersImport;
use Illuminate\Support\Facades\Auth;
use Spatie\Geocoder\Facades\Geocoder;
use Illuminate\Support\Facades\Storage;

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
        $action = Auth::user()->getAction();
        if($action){
            $routes = Route::where('action_id', $action['id'])->where('route_status_id',config('status.route_geplant'))->get(); 
        }
        else{
            $routes = null;
        }
        return view('admin.orders.index', compact('routes'));
    }

    public function createDataTables()
    {
        if(!Auth::user()->isAdmin()){
            $action = Auth::user()->getAction();
            if($action){
                $orders = Order::where('action_id', $action['id'])->get();
            }
            else{
                $orders = null;
            }

        }
        else{
            $orders = Order::all();
        }
        if($orders){

            return DataTables::of($orders)
            ->addColumn('name', function ($orders) {
                return $orders->address ? $orders->address['name'] : '';})
            ->addColumn('firstname', function ($orders) {
                return $orders->address ? $orders->address['firstname'] : '';})
            ->addColumn('street', function ($orders) {
                return $orders->address ? $orders->address['street'] : '';})
            ->addColumn('city', function ($orders) {
                return $orders->address ? $orders->address['city'] : '';})
            ->addColumn('plz', function ($orders) {
                return $orders->address ? $orders->address['plz'] : '';})
            ->addColumn('route', function ($orders) {
                return $orders->route ? $orders->route['name'] : '';})
            ->addColumn('status', function ($orders) {
                return $orders->order_status ? $orders->order_status['name'] : '';})
            ->addColumn('pick_up', function ($orders) {
                return $orders['pick_up'] ? 'Ja' : 'Nein';})
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
        else
        {
            return [];
        }

    }

    public function createRoute(Request $request)
    {
        $action = Auth::user()->getAction();
        if($request->name==''){
            $route = Route::FindOrFail($request->route_id);
        }
        else
        {
            $input['name'] = $request->name;
            $input['action_id'] =  $action['id'];
            $input['route_status_id'] = config('status.route_geplant');
            $input['user_id'] = Auth::user()->id;
            $route = Route::create($input);

               
        }
        Order::WhereIn('id',$request->id)->update(['route_id' => $route['id']]);

        $routes = Route::where('action_id', $action['id'])->where('route_status_id', config('status.route_geplant'))->get(); 
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
        $routes = Route::where('route_status_id', config('status.route_geplant'))->get();
        $routes = $routes->pluck('name','id')->all();
        return view('admin.orders.create', compact('routes'));
    }

    public function map()
    {
        $action= Auth::user()->getaction();        
        $orders = Order::where('action_id', $action['id']);
        $orders = $orders->with('address')->get();
        $cities = $action->addresses->unique('city')->pluck('city');
        $statuses = OrderStatus::pluck('name')->all();
        $center = $action->center;
        $key = $action['APIKey'];

        return view('admin.orders.map', compact('orders', 'cities', 'statuses', 'center', 'key'));
    }

    public function mapfilter(Request $request)
    {
        $action = Auth::user()->getaction();
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
            $orders->where('order_status_id',$order_status['id']);
        }
        
        $orders = $orders->with('address')->get();
        return $orders;
    }
    

    public function uploadFile(Request $request){
        if($request->hasFile('csv_file')){
          
            $array = (new OrdersImport)->toArray(request()->file('csv_file'));
            $importData_arr = $array[0];
            
      
            // Insert to MySQL database
            $user = Auth::user();
            $action = $user->getAction();
            $center = $action->center;
            $user = Auth::user();
            $action = $user->getAction();
            GeoCoder::setApiKey($action['APIKey']);
            GeoCoder::setCountry('CH');
            foreach($importData_arr as $importData){
                // return $importData; 
                $importData['abholung'] = $importData['abholung'] === "x" ? 1 : 0;
                $importData = array_map('trim', $importData);
                if($importData['abholung']){
                    $insertAddress = array(                   
                        "name"=> $importData['name'],
                        "firstname" => $importData['vorname'],
                        "street" => $center['street'],
                        "group_id"=> $user->group['id'],
                        "city" => $center['city'],
                        "plz" => $center['plz'],
                        "lat" => $center['lat'],
                        "lng" => $center['lng']);
                }
                else{
                    $insertAddress = array(                   
                        "name"=> $importData['name'],
                        "firstname" => $importData['vorname'],
                        "street" => $importData['strasse'],
                        "group_id"=>$user->group['id'],
                        "city" => $importData['ortschaft'],
                        "plz" => $importData['plz']);
                }

                $address = Address::firstOrCreate(['name' => $importData['name'], 'firstname' => $importData['vorname']], $insertAddress);

                if($address){
                    if(!$importData['abholung']){
                        $geocode = Geocoder::getCoordinatesForAddress($address['street'] . ', ' .$address['plz'] . ' '.$address['city']);
                        $lat = $geocode['lat'];
                        $lng = $geocode['lng'];
                        
                        $address->update(['lat' => $lat, 'lng' => $lng]);
                    }

                    if($importData['route']){
                        $insertRoute = array(                   
                            "name"=> $importData['route'],
                            "action_id" => $user->getAction,
                            "route_status_id" => config('status.route_geplant'));

                        $route = Route::firstOrCreate(['name' => $importData['route'],], $insertRoute);
                        $route_id = $route['id'];
                    }
                    else
                    {
                        $route_id = null;
                    }
        

                    $insertOrder = array(
                        
                        "quantity"=> $importData['anzahl'],
                        "route_id" => $route_id,
                        "action_id" => $action['id'],
                        "address_id"=> $address['id'],
                        "order_status_id" => config('status.order_offen'),
                        "pick_up" => $importData['abholung'],
                        "comments" => $importData['bemerkung']);
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
            $user = Auth::user();
            $action = $user->getAction();
            GeoCoder::setApiKey($action['APIKey']);
            GeoCoder::setCountry('CH');
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
        $input['order_status_id'] = config('status.order_offen');
        
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
        $action = Auth::user()->getAction();
        $routes = Route::where('action_id', $action['id'])->where('route_status_id',  config('status.route_unterwegs'))->get(); 
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
        $user = Auth::user();
        $action = $user->getAction();
        GeoCoder::setApiKey($action['APIKey']);
        GeoCoder::setCountry('CH');
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
