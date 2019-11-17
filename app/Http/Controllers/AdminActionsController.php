<?php

namespace App\Http\Controllers;

use App\Group;
use App\Action;
use App\ActionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminActionsController extends Controller
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
            $actions = Action::where('group_id', $group['id'])->get();
        }
        else{
            $actions = Action::all();
        }
        return view('admin.actions.index', compact('actions'));
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
        return view('admin.actions.create', compact('groups'));
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
        $input = $request->all();

        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $input['group_id'] = $group['id'];
        }

        $input['action_status_id'] = 5;
        Action::create($input);

        return redirect('admin/actions');
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
        $action = ACtion::findOrFail($id);
        $action_statuses = ActionStatus::pluck('name','id')->all();

        return view('admin.actions.edit', compact('action', 'action_statuses'));
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
        Action::findOrFail($id)->update($request->all());
        return redirect('/admin/actions');
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
        Action::findOrFail($id)->delete();
        return redirect('/admin/actions');
    }
}
