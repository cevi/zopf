<?php

namespace App\Http\Controllers;

use App\User;
use App\Route;
use App\Action;
use App\RouteStatus;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('admin.routes.index');
    }

    public function createDataTables()
    {
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
            $routes = Route::where('action_id', $action['id'])->get();

        }
        else{
            $routes = Route::all();
        }

        return DataTables::of($routes)
        ->addColumn('status', function ($routes) {
            return $routes->route_status['name'];
        })
        ->addColumn('user', function ($routes) {
            return $routes->user['username'];
        })
        ->addColumn('zopf_count', function ($routes) {
            return $routes->zopf_count();
        })
        ->addColumn('order_count', function ($routes) {
            return $routes->order_count();
        })
        ->addColumn('Actions', function($routes) {
            $buttons = '<a href='.\URL::route('routes.edit', $routes->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
            <a href='.\URL::route('routes.overview', $routes->id).' type="button" class="btn btn-info btn-sm">Übersicht</a>';
            // if($routes->route_status['id']==5){
            //     $buttons = $buttons .'
            //     <button data-remote='.\URL::route('routes.send', $routes->id).' id="send" class="btn btn-secondary btn-sm">Vorbereitet</button>';
            // };
            // if($routes->route_status['id']==10){
            //     $buttons = $buttons .'
            //     <button data-remote='.\URL::route('routes.send', $routes->id).' id="send" class="btn btn-secondary btn-sm">Lossenden</button>';
            // };
            return $buttons;
        })
        ->rawColumns(['Actions'])
        ->make(true);

    }

    public function overview($id)
    {
        //
        $route = Route::findOrFail($id);
        $orders = $route->orders;

        return view('admin.routes.overview', compact('route', 'orders'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $group = Auth::user()->group;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        return view('admin.routes.create', compact('users'));
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
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $input['name'] = $request->name;
        $input['action_id'] =  $action['id'];
        $input['route_status_id'] = 5;
        $input['user_id'] =  $request->user_id;
        Route::create($input);

        return redirect('/admin/routes/create');
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
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        $route_statuses = RouteStatus::pluck('name','id')->all();

        return view('admin.routes.edit', compact('route', 'users','route_statuses'));
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
        Route::findOrFail($id)->update($request->all());

        return redirect('/admin/routes');
    }

    public function send($id)
    {
        //
        $route = Route::findOrFail($id);
        $route->update(['route_status_id' =>  $route->route_status['id']  + 5]);
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
