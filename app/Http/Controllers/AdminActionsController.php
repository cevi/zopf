<?php

namespace App\Http\Controllers;

use AddAmountToLogbook;
use App\Group;
use App\Action;
use DataTables;
use App\Address;
use App\Logbook;
use App\ActionStatus;
use App\Helper\Helper;
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
            $text = ($actions->action_status_id === config('status.action_geplant')) ? 'Starten' : 'Abschliessen';
            $buttons = '<a href='.\URL::route('actions.edit', $actions->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>';
            $buttons .= ' <a href='.\URL::route('actions.complete', $actions->id).' type="button" class="btn btn-info btn-sm">' . $text . '</a>';
            $buttons .= ' <button data-remote='.\URL::route('actions.destroy', $actions->id).' class="btn btn-danger btn-sm">Löschen</button>';
            return $buttons;
        })
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
            $user = Auth::user();
            $action = $user->getAction();
            GeoCoder::setApiKey($request['APIKey']);
            GeoCoder::setCountry('CH');
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
        Action::create($input);
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
        $address=$action->center;
        if($address){
            $input = $request->all();
            GeoCoder::setApiKey($request['APIKey']);
            GeoCoder::setCountry('CH');
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
                $comment = 'Aktion '.$action['name'].' wurde gestartet.';
                Helper::CreateLogEntry(Auth::user()->id, $action['id'] ,$comment, now());
            }
            if($status_id === config('status.action_abgeschlossen')){
                $comment = 'Aktion '.$action['name'].' wurde abgeschlossen.';
                Helper::CreateLogEntry(Auth::user()->id, $action['id'] ,$comment, now());
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
        if($action->action_status_id === config('status.action_geplant')){
            $action->action_status_id = config('status.action_aktiv');
            $comment = 'Aktion '.$action['name'].' wurde gestartet.';
        }
        else{
            $action->action_status_id = config('status.action_abgeschlossen');
            $comment = 'Aktion '.$action['name'].' wurde abgeschlossen.';
        }
        Helper::CreateLogEntry(Auth::user()->id, $action['id'], $comment, now());
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
