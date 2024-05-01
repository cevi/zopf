<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Helper\Helper;
use App\Models\ActionUser;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Help;
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
        if (! Auth::user()->isAdmin()) {
            $roles = Role::where('id', '>', config('status.role_administrator'))->pluck('name', 'id')->all();
        } else {
            $roles = Role::pluck('name', 'id')->all();
        }
        $title = 'Leiter';
        $help = Help::where('title', $title)->first();

        return view('admin.users.index', compact('roles', 'title', 'help'));
    }

    public function createDataTables()
    {
        //
        if (! Auth::user()->isAdmin()) {
            $group = Auth::user()->group;
            $users = $group->allUsers;
        } else {
            $users = User::all();
        }

        return DataTables::of($users)
            ->addColumn('group', function (User $user) {
                if (Auth::user()->isAdmin()) {
                    $group_name = $user->group ? $user->group['name'] : '';
                } else {
                    $group_name = Auth::user()->group['name'];
                }

                return $group_name;
            })
            ->addColumn('role', function (User $user) {
                $role_name = '';
                if (Auth::user()->isAdmin()) {
                    $role_name = $user->role ? $user->role['name'] : '';
                } else {
                    $action = Auth::user()->action;
                    if ($action) {
                        $action_user = ActionUser::where('action_id', '=', $action['id'])->where('user_id', '=', $user['id'])->first();
                        if ($action_user) {
                            $role_name = $action_user->role['name'];
                        }
                    } else {
                        $group = Auth::user()->group;
                        if ($group) {
                            $group_user = GroupUser::where('group_id', '=', $group['id'])->where('user_id', '=', $user['id'])->first();
                            if ($group_user) {
                                $role_name = $group_user->role['name'];
                            }
                        }
                    }
                }

                return $role_name;
            })
            ->addIndexColumn()
            ->addColumn('Actions', function ($users) {
                return '<a href='.\URL::route('users.edit', $users->id).' type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Bearbeiten</a>
                <button data-remote='.\URL::route('users.destroy', $users->id).' class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-xs px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Löschen</button>';
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            if (trim($request->password) == '') {
                $input = $request->except('password');
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
            }

            if (! $aktUser->isAdmin()) {
                $group = $aktUser->group;
                $action = $aktUser->action;
                $input['group_id'] = $group['id'];
                if (isset($action)) {
                    $input['action_id'] = $action['id'];
                }
            }
            $input['email_verified_at'] = now();

            $user = User::create($input);
            UserCreated::dispatch($user);
            if (isset($group)) {
                Helper::UpdateGroup($user, $group);
            }
            if (isset($action)) {
                Helper::UpdateACtion($user, $action);
            }
        }

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
        $users = User::where('role_id', '<>', config('status.role_administrator'))->search($request->get('term'))->get();

        $usersDiff = $users->diff($allusers);
        foreach ($usersDiff as $user) {
            $data[] = ['username' => $user->username, 'email' => $user->email, 'id' => $user->id];
        }
        if (count($data)) {
            return $data;
        } else {
            return ['username' => '', 'email' => '', 'id' => ''];
        }
    }

    public function add(Request $request)
    {

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $input = $request->all();
            if ($input['user_id']) {
                $group = $aktUser->group;
                $user = User::findOrFail($input['user_id']);
                GroupUser::create([
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'role_id' => $input['role_id_add'], ]
                );
                $action = $aktUser->action;
                ActionUser::create([
                    'user_id' => $user->id,
                    'action_id' => $action->id,
                    'role_id' => $input['role_id_add'], ]
                );
            }
        }

        return redirect('/admin/users');
    }

    public function AddGroupUsersToAction()
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $action = $aktUser->action;
            $group = $aktUser->group;
            Helper::AddGroupUsersToAction($group, $action);
        }

        return true;
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
        $title = 'Leiter - Bearbeiten';
        $user = User::findOrFail($id);
        $groups = Group::pluck('name', 'id')->all();
        $roles = Role::pluck('name', 'id')->all();
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Leiter';
        $help['main_route'] = '/admin/users';

        return view('admin.users.edit', compact('user', 'roles', 'groups', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $user = User::findOrFail($id);

            if (trim($request->password) == '') {
                $input = $request->except('password');
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
            }

            $user->update($input);
        }

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

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            User::findOrFail($id)->delete();
        }
    }
}
