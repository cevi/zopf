<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;
use DataTables;

class AdminGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::pluck('username','id')->all();
        return view('admin.groups.index', compact('users'));
    }

    public function createDataTables()
    {
        //
        $groups = Group::all();


        return DataTables::of($groups)
            ->addColumn('groupleader', function ($groups) {
                return $groups->user['username'];})
            ->addColumn('Actions', function($groups) {
                return '<a href='.\URL::route('groups.edit', $groups->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
                <button data-remote='.\URL::route('groups.destroy', $groups->id).' class="btn btn-danger btn-sm">Löschen</button>';
            })
            // ->addColumn('checkbox', function ($users) {
                // return '<input type="checkbox" id="'.$users->id.'" name="someCheckbox" />';
            // })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        Group::create($request->all());

        return redirect('admin/groups');
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
        $group = Group::findOrFail($id);
        $users = User::pluck('username','id')->all();

        return view('admin.groups.edit', compact('group', 'users'));
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
        Group::findOrFail($id)->update( $request->all());
        return redirect('admin/groups');
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
        Group::findOrFail($id)->delete();
        return redirect('admin/groups');
    }
}
