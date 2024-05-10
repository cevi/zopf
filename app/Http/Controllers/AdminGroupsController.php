<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Help;
use App\Models\User;
use App\Models\Group;
use App\Helper\Helper;
use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $title = 'Gruppen';
        $help = Help::where('title', $title)->first();
        $users = Auth::user()->pluck('username', 'id');

        return view('admin.groups.index', compact('users', 'title', 'help'));
    }

    public function createDataTables()
    {
        //
        if (!Auth::user()->isAdmin()) {
            $group = Auth::user()->group;
            $groups = Group::where('id', $group['id'])->get();
        } else {
            $groups = Group::all();
        }

        return DataTables::of($groups)
            ->addColumn('groupleader', function ($groups) {
                return $groups->user ? $groups->user['username'] : '';
            })
            ->addColumn('Actions', function ($groups) {
                return '<a href='.\URL::route('groups.edit', $groups->id).' type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Bearbeiten</a>';
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

        $user = Auth::user();
        if (! $user->demo) {
            Group::create($request->all());
        }

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
        $users = $group->allUsers()->pluck('username', 'user_id')->all();
        $title = 'Gruppe Bearbeiten';
        $help = Help::where('title', $title)->first();

        return view('admin.groups.edit', compact('group', 'users', 'title', 'help'));
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

        $user = Auth::user();
        if (! $user->demo) {
            Group::findOrFail($id)->update($request->all());
        }

        return redirect('admin/groups');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $aktUser = Auth::user();
        if (!$aktUser->demo) {
            $actions = $group->actions;
            foreach($actions as $action){
                Helper::deleteAction($action);
            } 
            if($group->actions()->count()===0){
                $group->delete();
            }
            
            $group_global = Group::where('global',true)->first();
            $users = $group->allUsers;
            foreach($users as $user){
                if($group===$user->group){
                    Helper::updateGroup($user, $group_global);
                }
                GroupUser::where('group_id', $group->id)->where('user_id', $user->id)->delete();
            }
        }

        return redirect('admin/groups');
    }
}
