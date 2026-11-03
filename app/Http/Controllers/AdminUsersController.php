<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Helper\Helper;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Help;
use App\Models\Role;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        // Nur Rollen, die der Benutzer vergeben darf (höchstens die eigene)
        $roles = Role::all()->filter(fn ($role) => Auth::user()->can('assignRole', [User::class, $role->id]))->pluck('name', 'id')->all();
        $title = 'Leiter';
        $help = Help::where('title', $title)->first();

        return view('admin.users.index', compact('roles', 'title', 'help'));
    }

    public function createDataTables()
    {
        //
        if (! Auth::user()->isAdmin()) {
            $action = Auth::user()->action;
            $users = $action->allUsers;
        } else {
            $users = User::all();
        }

        return DataTables::of($users)
            ->addColumn('username', function (User $user) {
                // Name maskieren: Spalte ist HTML (rawColumns)
                return '<a href="'.e(\URL::route('users.edit', $user)).'" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">'.e($user['username']).'</a>';
            })
            ->addColumn('group', function (User $user) {
                if (Auth::user()->isAdmin()) {
                    $group_name = $user->group ? $user->group['name'] : '';
                } else {
                    $group_name = Auth::user()->group['name'];
                }

                return $group_name;
            })
            ->addColumn('picture', function ($user) {
                return '<img class="img-user" alt="" src="'.e($user->getAvatar()).'">';
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
            // ->addColumn('checkbox', function ($users) {
                // return '<input type="checkbox" id="'.$users->id.'" name="someCheckbox" />';
            // })
            ->rawColumns(['picture', 'username'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //

        $aktUser = Auth::user();
        if ($request->filled('role_id')) {
            $this->authorize('assignRole', [User::class, (int) $request->input('role_id')]);
        }
        if (! $aktUser->demo) {
            // Nur die Felder des Formulars; Gruppe und Aktion setzt bei Nicht-Admins der Controller.
            $input = $request->only($aktUser->isAdmin()
                ? ['username', 'email', 'role_id', 'is_active', 'group_id']
                : ['username', 'email', 'role_id', 'is_active']);
            if (trim((string) $request->password) != '') {
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
                Helper::UpdateGroup($user, $group, true);
            }
            if (isset($action)) {
                Helper::UpdateAction($user, $action, true);
            }
            Helper::updateAvatar($request, $user);
        }

        return redirect('/admin/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    public function searchResponseUser(Request $request)
    {
        // Mindestens 3 Zeichen, Antwort ohne E-Mail-Adressen (Liste aller Personen sonst offen)
        if (mb_strlen(trim((string) $request->get('term'))) < 3) {
            return [];
        }
        $group = Auth::user()->group;
        $allusers = $group->users;
        $users = User::where('role_id', '<>', config('status.role_administrator'))->search($request->get('term'))->get();

        $usersDiff = $users->diff($allusers);
        $data = [];
        foreach ($usersDiff as $user) {
            $data[] = ['username' => $user->username, 'id' => $user->id];
        }

        return $data;
    }

    public function add(Request $request)
    {

        $aktUser = Auth::user();
        $this->authorize('assignRole', [User::class, (int) $request->input('role_id_add')]);
        if (! $aktUser->demo) {
            $input = $request->only(['user_id', 'role_id_add']);
            if ($input['user_id'] ?? null) {
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
     * @return Response
     */
    public function edit($id)
    {
        //
        $title = 'Leiter - Bearbeiten';
        $user = User::findOrFail($id);
        $this->authorize('manage', $user);
        $groups = Group::pluck('name', 'id')->all();
        // Nur Rollen, die der Benutzer vergeben darf (höchstens die eigene)
        $roles = Role::all()->filter(fn ($role) => Auth::user()->can('assignRole', [User::class, $role->id]))->pluck('name', 'id')->all();
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Leiter';
        $help['main_route'] = '/admin/users';

        return view('admin.users.edit', compact('user', 'roles', 'groups', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $user = User::findOrFail($id);
            $this->authorize('manage', $user);
            if ($request->filled('role_id')) {
                $this->authorize('assignRole', [User::class, (int) $request->input('role_id')]);
            }

            // Nur die Felder des Formulars; die Gruppe setzt nur der Admin direkt.
            $input = $request->only($aktUser->isAdmin()
                ? ['username', 'email', 'role_id', 'is_active', 'group_id']
                : ['username', 'email', 'role_id', 'is_active']);
            if (trim((string) $request->password) != '') {
                $input['password'] = bcrypt($request->password);
            }
            if (! $aktUser->isAdmin()) {
                $group = $aktUser->group;
                $action = $aktUser->action;
            }
            $user->update($input);
            if (isset($group)) {
                Helper::UpdateGroup($user, $group, true);
            }
            if (isset($action)) {
                Helper::UpdateAction($user, $action, true);
            }
            Helper::updateAvatar($request, $user);
        }

        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(User $user)
    {
        $this->authorize('manage', $user);
        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $action = Auth::user()->action;
            if ($action === $user->action) {
                $action_global = Action::where('global', true)->first();
                Helper::updateAction($user, $action_global);
            }
            ActionUser::where('action_id', $action->id)->where('user_id', $user->id)->delete();
            // $group = Auth::user()->group;
            // if($group===$user->group){
            //     $group_global = Group::where('global', true)->first();
            //     Helper::updateGroup($user, $group_global);
            // }
            // GroupUser::where('group_id', $group->id)->where('user_id', $user->id)->delete();
        }

        return true;
    }
}
