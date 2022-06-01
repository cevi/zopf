<?php

namespace App\Http\Controllers;

use App\Logbook;
use App\User;
use Auth;
use Illuminate\Http\Request;

class AdminLogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function edit(Logbook $logbook)
    {
        //
        $group = Auth::user()->group;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        return view('admin.logbooks.edit', compact('logbook', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logbook $logbook)
    {
        //
        $logbook->update($request->all());
        return redirect('/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logbook $logbook)
    {
        //
        $logbook->delete();
        return redirect('/admin');
    }
}
