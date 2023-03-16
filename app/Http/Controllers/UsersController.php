<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    //
    public function index(User $user)
    {
        $aktUser = Auth::user();
        if (! $aktUser) {
            return redirect('/home');
        }
        if ($aktUser->id == $user->id) {
            $title = 'Eigenes Profil';

            return view('home.user', compact('aktUser', 'title'));
        } else {
            return redirect()->back();
        }
    }

    public function update(Request $request, User $user)
    {
        if(!Auth::user()->demo){
            if (trim($request->password) == '') {
                $input = $request->except('password');
                $user->update($input);
            } else {
                $request->validate([
                    'password' => ['required', 'confirmed'],
                ]);
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
                $input['password_change_at'] = now();
                $user->update($input);
                Session::flash('message', 'Passwort erfolgreich verändert!');
            }
        }

        return redirect('/home');
    }
}
