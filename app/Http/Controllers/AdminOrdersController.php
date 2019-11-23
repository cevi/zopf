<?php

namespace App\Http\Controllers;

use App\Order;
use App\Action;
use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->get();
            $orders = Order::where('action_id', $action['id'])->get();

        }
        else{
            $orders = Order::all();
        }
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $routes = Route::pluck('name','id')->all();
        return view('admin.orders.create', compact('routes'));
    }

    public function searchResponseAddress(Request $request)
    {
        $query = $request->get('term','');
        $addresses=\DB::table('addresses');
        if($request->type=='address_name'){
            $addresses->where('name','LIKE','%'.$query.'%');
        }
        if($request->type=='address_firstname'){
            $addresses->where('firstname','LIKE','%'.$query.'%');
        }

        $addresses = $addresses->get();        
        $data=array();
        foreach ($addresses as $address) {
                $data[]=array('name'=>$address->name,'firstname'=>$address->firstname);
        }
        if(count($data))
             return $data;
        else
            return ['name'=>'','firstname'=>''];
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
    }
}
