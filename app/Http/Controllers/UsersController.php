<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\User;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
            $title = 'Profil';
            $help = Help::where('title',$title)->first();

            return view('home.user', compact('aktUser', 'title', 'help'));
        } else {
            return redirect()->back();
        }
    }

    public function update(Request $request, User $user)
    {
        if (! Auth::user()->demo) {
            if (trim($request->password) == '') {
                $input = $request->except('password');
                $user->update($input);
                Helper::updateAvatar($request, $user);
            } else {
                $request->validate([
                    'password' => ['required', 'confirmed'],
                ]);
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
                $input['password_change_at'] = now();
                $user->update($input);
                Helper::updateAvatar($request, $user);
                Session::flash('message', 'Passwort erfolgreich verändert!');
            }
        }

        return redirect('/home');
    }
}
