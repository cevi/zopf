<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Help;
use Auth;
use Illuminate\Http\Request;
use Validator;

class GroupsController extends Controller
{
    //
    public function create()
    {
        //
        $title = 'Gruppe - Erfassen';
        $users = Auth::user()->pluck('username', 'id');
        $help = Help::where('title',$title)->first();

        return view('home.groups.create', compact('users', 'title', 'help'));
    }

    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'unique:groups',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous())
                ->withErrors($validator)
                ->withInput();
        }


        $aktUser = Auth::user();
        if (!$aktUser->demo) {
            $input = $request->all();
            $input['user_id'] = $aktUser->id;
            $input['global'] = false;
            $group = Group::create($input);
            $aktUser->update(['group_id' => $group->id, 'role_id' => config('status.role_groupleader')]);
            GroupUser::create([
                'user_id' => $aktUser->id,
                'group_id' => $group->id,
                'role_id' => config('status.role_groupleader'),
            ]);
        }
        return redirect('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
        $title = 'Gruppe - Bearbeiten';
        $help = Help::where('title',$title)->first();

        return view('home.groups.edit', compact('group', 'title', 'help'));
    }

    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'unique:groups',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous())
                ->withErrors($validator)
                ->withInput();
        }

        $aktUser = Auth::user();
        if (!$aktUser->demo) {
            $input = $request->all();
            $group->update($input);
        }

        return redirect('home');
    }

    public function updateGroup(Request $request, Group $group)
    {
        //

        $aktUser = Auth::user();
        if (!$aktUser->demo) {
            Helper::updateGroup(Auth::user(), $group);
        }

        return redirect('/home');
    }
}
