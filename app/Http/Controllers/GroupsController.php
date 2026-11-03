<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Help;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class GroupsController extends Controller
{
    //
    public function create()
    {
        //
        $title = 'Gruppe - Erfassen';
        $users = Auth::user()->pluck('username', 'id');
        $help = Help::where('title', $title)->first();

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
        if (! $aktUser->demo) {
            // Nur das Formularfeld; Besitzer ist immer die eigene Person, nie global
            $group = Group::create([
                'name' => $request->input('name'),
                'user_id' => $aktUser->id,
                'global' => false,
            ]);
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
     * @return Response
     */
    public function edit(Group $group)
    {
        $this->authorize('manage', $group);
        $title = 'Gruppe - Bearbeiten';
        $help = Help::where('title', $title)->first();

        return view('home.groups.edit', compact('group', 'title', 'help'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('manage', $group);

        $validator = Validator::make($request->all(), [
            'name' => 'unique:groups',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous())
                ->withErrors($validator)
                ->withInput();
        }

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            $group->update($request->only(['name']));
        }

        return redirect('home');
    }

    public function updateGroup(Request $request, Group $group)
    {
        $this->authorize('switchTo', $group);

        $aktUser = Auth::user();
        if (! $aktUser->demo) {
            Helper::updateGroup(Auth::user(), $group);
        }

        return redirect('/home');
    }
}
