<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\ActionUser;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Role;
use App\Models\User;
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
        if(!Auth::user()->isAdmin()){
            $roles = Role::where('id','>',config('status.role_administrator'))->pluck('name','id')->all();
        } else {
            $roles = Role::pluck('name','id')->all();

        }
        $title = 'Leiter';
        return view('admin.users.index', compact('roles', 'title'));
        // return view('admin.users.index', compact('users'));
    }

    public function createDataTables()
    {
        //
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $users = $group->allUsers;
        }
        else{
            $users = User::all();
        }

        return DataTables::of($users)
            ->addColumn('group', function (User $user) {
                if(Auth::user()->isAdmin()) {
                    $group_name = $user->group ? $user->group['name'] : '';
                }
                else{
                    $group_name = Auth::user()->group['name'];
                }
                return $group_name;
                })
            ->addColumn('role', function (User $user) {
                $role_name = '';
                if(Auth::user()->isAdmin()) {
                    $role_name = $user->role ? $user->role['name'] : '';
                }
                else{
                    $action = Auth::user()->action;
                    if($action){
                        $action_user = ActionUser::where('action_id','=',$action['id'])->where('user_id','=',$user['id'])->first();
                        if($action_user){
                            $role_name = $action_user->role['name'];
                        }
                    }
                    else{
                        $group = Auth::user()->group;
                        if($group){
                            $group_user = GroupUser::where('group_id','=',$group['id'])->where('user_id','=',$user['id'])->first();
                            if($group_user){
                                $role_name = $group_user->role['name'];
                            }
                        }
                    }
                }
                return $role_name;
            })
            ->addIndexColumn()
            ->addColumn('Actions', function($users) {
                return '<a href='.\URL::route('users.edit', $users->id).' type="button" class="btn btn-primary btn-sm">Bearbeiten</a>
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

        $user = User::create($input);
        UserCreated::dispatch($user);

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

    public function searchResponseUser(Request $request)
    {
        $group = Auth::user()->group;
        $allusers = $group->users;
        $users = User::where('role_id','<>',config('status.role_administrator'))->search($request->get('term'))->get();
        return $users->diff($allusers);
    }

    public function add(Request $request)
    {
        $input = $request->all();
        if($input['user_id']){
            $aktUser = Auth::user();
            $group = $aktUser->group;
            $user = User::findOrFail($input['user_id']);
            GroupUser::create([
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'role_id' => $input['role_id_add']]
            );
            $action = $aktUser->action;
            ActionUser::create([
                    'user_id' => $user->id,
                    'action_id' => $action->id,
                    'role_id' => $input['role_id_add']]
            );
        }
        return redirect('/admin/users');
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
        $title = 'Leiter Bearbeiten';
        $user = User::findOrFail($id);
        $groups = Group::pluck('name','id')->all();
        $roles = Role::pluck('name','id')->all();

        return view('admin.users.edit', compact('user', 'roles','groups', 'title'));
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
