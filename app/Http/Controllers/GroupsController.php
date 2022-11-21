<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Group;
use App\Models\GroupUser;
use Auth;
use Illuminate\Http\Request;
use Validator;

class GroupsController extends Controller
{
    //
    public function create()
    {
        //
        $title = 'Gruppe Erfassen';
        $users = Auth::user()->pluck('username', 'id');

        return view('home.groups.create', compact('users', 'title'));
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

        $input = $request->all();
        $user = Auth::user();
        $input['user_id'] = $user->id;
        $input['global'] = false;
        $group = Group::create($input);
        $user->update(['group_id' => $group->id, 'role_id' => config('status.role_groupleader')]);
        GroupUser::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role_id' => config('status.role_groupleader'), ]);

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
        $title = 'Gruppe Bearbeiten';

        return view('home.groups.edit', compact('group', 'title'));
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

        $input = $request->all();
        $group->update($input);

        return redirect('home');
    }

    public function updateGroup(Request $request, Group $group)
    {
        //
        Helper::updateGroup(Auth::user(), $group);

        return redirect('/home');
    }
}
