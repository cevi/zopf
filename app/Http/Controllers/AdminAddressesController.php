<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Geocoder\Facades\Geocoder;

class AdminAddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $addresses = Address::where('group_id', $group['id'])->get();

        }
        else{
            $addresses = Address::all();
        }
        return view('admin.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $groups = Group::pluck('name','id')->all();
        return view('admin.addresses.create', compact('groups'));
    }

    public function searchResponseCity(Request $request)
    {
        // $cities=\DB::table('cities');
        $query = $request->get('term','');
        // $cities->where('plz','LIKE','%'.$query.'%')->orWhere('name','LIKE','%'.$query.'%');
        //    $cities=$cities->get();
        // $data=array();
        $cities = City::search($request->get('term'))->get();
        foreach ($cities as $city) {
                $data[]=array('name'=>$city->name,'plz'=>$city->plz,'id'=>$city->id);
        }
        if(count($data))
             return $data;
        else
            return ['name'=>'','plz'=>'','id'=>''];
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
        $action = $user->action;
        $city=City::Where('id',$request->city_id)->first();
        if(!$city){
            return back()->withInput()->withErrors(['errors' => ['Ortschaft nicht gefunden.']]);
        }
        else{
            $input = $request->all();
            $input['city_id'] = $city->id;
        }
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $input['group_id'] = $group['id'];
        }
        GeoCoder::setApiKey($input['APIKey']);
        GeoCoder::setCountry('CH');
        $geocode =  GeoCoder::getCoordinatesForAddress($input['street'] . ', ' . $city->plz . ' '.$city->name);
        $input['lat'] = $geocode['lat'];
        $input['lng'] = $geocode['lng'];

        // return $input;

        Address::create($input);

        return redirect('/admin/addresses');
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
        $address = Address::findOrFail($id);
        $groups = Group::pluck('name','id')->all();
        $city_id = $address->city_id;
        $city = City::Where('id',$city_id)->first();
        $city_name = $city->name;
        $city_plz = $city->plz;

        return view('admin.addresses.edit', compact('address','groups', 'city_id', 'city_name', 'city_plz'));
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
        $address = Address::findOrFail($id);
        $city=City::Where('name',$request->city_name)->Where('plz',$request->city_plz)->first();
        if(!$city){
            return back()->withInput()->withErrors(['errors' => ['Ortschaft nicht gefunden.']]);
        }
        else{
            $input = $request->all();
            $input['city_id'] = $city->id;
        }
        $user = Auth::user();
        $action = $user->action;
        GeoCoder::setApiKey($action['APIKey']);
        GeoCoder::setCountry('CH');

        $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' . $city->plz . ' '.$city->name);
        $input['lat'] = $geocode['lat'];
        $input['lng'] = $geocode['lng'];

        $address->update($input);
        return redirect('admin/addresses');
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
        Address::findOrFail($id)->delete();
        return redirect('/admin/addresses');
    }
}
