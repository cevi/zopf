<?php

namespace App\Http\Controllers;

use App\Group;
use App\Order;
use App\Action;
use DataTables;
use App\Address;
use App\Logbook;
use App\ActionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Geocoder\Facades\Geocoder;

class AdminActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return view('admin.actions.index');
    }

    public function createDataTables()
    {
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $actions = Action::where('group_id', $group['id'])->get();
        }
        else{
            $actions = Action::all();
        }

        return DataTables::of($actions)
        ->addColumn('group', function ($actions) {
            return $actions->group['name'];})
        ->addColumn('status', function ($actions) {
            return $actions->action_status['name'];})
        ->addColumn('Actions', function($actions) {
            return '<a href='.\URL::route('actions.edit', $actions->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
            <a href='.\URL::route('actions.complete', $actions->id).' type="button" class="btn btn-info btn-sm">Abschliessen</a>
            <button data-remote='.\URL::route('actions.destroy', $actions->id).' class="btn btn-danger btn-sm">Löschen</button>';
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
        $groups = Group::pluck('name','id')->all();
        return view('admin.actions.create', compact('groups'));
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

        $address=Address::Where('id',$request->address_id)->first();
        if(!$address){
            $input = $request->all();
            if(!Auth::user()->isAdmin()){
                $group = Auth::user()->group;
                $input['group_id'] = $group['id'];
            }
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];
            $input['center'] = true;

            // return $input;

            $address = Address::create($input);
        }
        else{
            $input = $request->all(); 
        }
        $input['address_id'] = $address['id']; 

        $input['action_status_id'] = config('status.action_geplant');
        $action = Action::create($input);
        Logbook::create(['user_id' => Auth::user()->id, 'action_id' => $action['id'], 'comments' => 'Aktion '.$action['name'].' wurde geplant.']);

        return redirect('admin/actions');
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
        $action = Action::findOrFail($id);
        $action_statuses = ActionStatus::pluck('name','id')->all();

        return view('admin.actions.edit', compact('action', 'action_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $action = Action::findOrFail($id);
        $address=$action->address;
        if($address){
            $input = $request->all();
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];
            $input['center'] = true;
            
            $address->update($input);
        }   
        else{            
            $input = $request->all();
            if(!Auth::user()->isAdmin()){
                $group = Auth::user()->group;
                $input['group_id'] = $group['id'];
            }
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' .$input['plz'] . ' '.$input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];
            $input['center'] = true;

            // return $input;

            $address = Address::create($input);  
            $input['address_id'] = $address['id'];
        }     
        $status_id = (int)$input['action_status_id'];
        if($status_id!=$action['action_status_id']){
            if($status_id === config('status.action_aktiv')){
                Logbook::create(['user_id' => Auth::user()->id, 'action_id' => $action['id'], 'comments' => 'Aktion '.$action['name'].' wurde gestartet.']); 
            }
            if($status_id === config('status.action_abgeschlossen')){
                Logbook::create(['user_id' => Auth::user()->id, 'action_id' => $action['id'], 'comments' => 'Aktion '.$action['name'].' wurde abgeschlossen.']); 
            }
        }
        $action->update($input);
        return redirect('/admin/actions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request, $id)
    {
        //
        $action = Action::findOrFail($id);
        $action->action_status_id = 10;
        $action->update($request->all());
        return redirect('/admin/actions');
    }

    public function destroy($id)
    {
        //
        Action::findOrFail($id)->delete();
        return redirect('/admin/actions');
    }
}
