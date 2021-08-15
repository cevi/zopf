<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Group;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.users.index');
        // return view('admin.users.index', compact('users'));
    }

    public function createDataTables()
    {
        //
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $users = User::where('group_id', $group['id'])->get();
        }
        else{
            $users = User::all();
        }

        return DataTables::of($users)
            ->addColumn('group', function (User $user) {
                return $user->group ?$user->group['name'] : '';})
            ->addColumn('role', function (User $user) {
                return $user->role ? $user->role['name'] : '';})
            ->addColumn('active', function (User $user) {
                if($user->is_active){
                    return 'Aktiv';
                }
                else{
                    return 'Inaktiv';
                }})
            ->addIndexColumn()
            ->addColumn('Actions', function($users) {
                return '<a href='.\URL::route('users.edit', $users->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
                <button data-remote='.\URL::route('users.destroy', $users->id).' class="btn btn-danger btn-sm">Löschen</button>';
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
        if(!Auth::user()->isAdmin()){
            $roles = Role::where('is_admin', false)->pluck('name','id')->all();
        } else {
            $roles = Role::pluck('name','id')->all();

        }
        $groups = Group::pluck('name','id')->all();
        return view('admin.users.create', compact('roles','groups'));
    
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
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $input['group_id'] = $group['id'];
        }

        User::create($input);

        return redirect('/admin/users');
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
        $user = User::findOrFail($id);
        $groups = Group::pluck('name','id')->all();
        $roles = Role::pluck('name','id')->all();

        return view('admin.users.edit', compact('user', 'roles','groups'));
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
        $user = User::findOrFail($id);

        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        $user->update($input);
        return redirect('/admin/users');
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
        User::findOrFail($id)->delete();
    }
}
