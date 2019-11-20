<?php

namespace App\Http\Controllers;

use App\City;
use App\Group;
use App\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function searchResponse(Request $request)
    {
        $query = $request->get('term','');
        $cities=\DB::table('cities');
        if($request->type=='city_name'){
            $cities->where('name','LIKE','%'.$query.'%');
        }
        if($request->type=='city_plz'){
            $cities->where('plz','LIKE','%'.$query.'%');
        }

           $cities=$cities->get();        
        $data=array();
        foreach ($cities as $city) {
                $data[]=array('name'=>$city->name,'plz'=>$city->plz);
        }
        if(count($data))
             return $data;
        else
            return ['name'=>'','plz'=>''];
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
        $city=City::Where('name',$request->city_name)->Where('plz',$request->city_plz)->first();
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
        $city_name = City::Where('id',$address->city_id)->first()->name;
        $city_plz =City::Where('id',$address->city_id)->first()->plz;

        return view('admin.addresses.edit', compact('address','groups', 'city_name', 'city_plz'));
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
