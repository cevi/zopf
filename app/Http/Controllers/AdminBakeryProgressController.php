<?php

namespace App\Http\Controllers;

use App\BakeryProgress;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBakeryProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = "Backstuben Verlauf";
        $group = Auth::user()->group;
        $action = Auth::user()->getAction();
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        $progress = BakeryProgress::where('action_id','=',$action['id'])->orderby('when')->get();

        foreach($progress  as $act_progress){
            $graphs[0]['time'][] = date('H:i:s', strtotime($act_progress['when']));
            $graphs[1]['data'][] = $act_progress['raw_material'];
            $graphs[2]['data'][] = $act_progress['dough'];
            $graphs[3]['data'][] = $act_progress['braided'];
            $graphs[4]['data'][] = $act_progress['baked'];
            $graphs[5]['data'][] = $act_progress['delivered'];
        }

        $graphs[1]['label'] = 'Roh-Materialien';
        $graphs[1]['color'] = '#B47EB3';

        $graphs[2]['label'] = 'Teig';
        $graphs[2]['color'] = '#FDF5BF';

        $graphs[3]['label'] = 'Gezöpfelt';
        $graphs[3]['color'] = '#FFD5FF';

        $graphs[4]['label'] = 'Gebacken';
        $graphs[4]['color'] = '#92D1C3';

        $graphs[5]['label'] = 'Ausgeliefert';
        $graphs[5]['color'] = '#8BB8A8';

        return view('admin.progress.index', compact('users', 'progress', 'title', 'graphs'));
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

        $action = Auth::user()->getAction();
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['action_id'] =  $action['id'];
        $input['total'] = $input['raw_material'] + $input['dough'] + $input['braided'] + $input['baked'] + $input['delivered'];
        BakeryProgress::create($input);

        return redirect('/admin/progress');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BakeryProgress  $bakeryProgress
     * @return \Illuminate\Http\Response
     */
    public function show(BakeryProgress $bakeryProgress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BakeryProgress  $bakeryProgress
     * @return \Illuminate\Http\Response
     */
    public function edit(BakeryProgress $progress)
    {
        //

        $title = "Backstuben Verlauf Bearbeiten";
        $group = Auth::user()->group;
        $action = Auth::user()->getAction();
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();

        return view('admin.progress.edit', compact('users', 'progress', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BakeryProgress  $bakeryProgress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BakeryProgress $progress)
    {
        //

        $input = $request->all();
        $input['total'] = $input['raw_material'] + $input['dough'] + $input['braided'] + $input['baked'] + $input['delivered'];
        $progress->update($input);

        return redirect('/admin/progress');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BakeryProgress  $bakeryProgress
     * @return \Illuminate\Http\Response
     */
    public function destroy(BakeryProgress $progress)
    {
        //
        $progress->delete();
        return redirect('/admin/progress');
    }
}
