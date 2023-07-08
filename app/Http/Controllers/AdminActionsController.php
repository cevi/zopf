<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Helper\Helper;
use App\Models\Action;
use App\Models\ActionStatus;
use App\Models\ActionUser;
use App\Models\Address;
use App\Models\Group;
use App\Models\Help;
use App\Models\Logbook;
use DataTables;
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

        $title = 'Aktionen';
        $help = Help::where('title',$title)->first();

        return view('admin.actions.index', compact('title', 'help'));
    }

    public function createDataTables()
    {
        if (! Auth::user()->isAdmin()) {
            $group = Auth::user()->group;
            $actions = Action::where('group_id', $group['id'])->get();
        } else {
            $actions = Action::all();
        }

        return DataTables::of($actions)
        ->addColumn('group', function ($actions) {
            return $actions->group['name'];
        })
        ->addColumn('status', function ($actions) {
            return $actions->action_status['name'];
        })
        ->addColumn('Actions', function ($actions) {
            $text = ($actions->action_status_id === config('status.action_geplant')) ? 'Starten' : 'Abschliessen';
            $buttons = '<a href='.\URL::route('actions.edit', $actions).' type="button" class="btn btn-primary btn-sm">Bearbeiten</a>';
            $buttons .= ' <a href='.\URL::route('actions.complete', $actions).' type="button" class="btn btn-info btn-sm">'.$text.'</a>';
            $buttons .= ' <button data-remote='.\URL::route('actions.destroy', $actions).' class="btn btn-danger btn-sm">Löschen</button>';

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
        $title = 'Aktion - Erfassen';
        $groups = Group::pluck('name', 'id')->all();
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Aktionen';
        $help['main_route'] =  '/admin/actions';

        return view('admin.actions.create', compact('groups', 'title', 'help'));
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
        $aktUser = Auth::user();
        if(!$aktUser->demo) {
            $address = Address::Where('id', $request->address_id)->first();
            $group = $aktUser->group;
            if (!$address) {
                $input = $request->all();
                if (!$aktUser->isAdmin()) {
                    $input['group_id'] = $group['id'];
                }
                $geocoder = Helper::getGeocoder($input['APIKey']);
                $geocode = $geocoder->getCoordinatesForAddress($input['street'] . ', ' . $input['plz'] . ' ' . $input['city']);
                $input['lat'] = $geocode['lat'];
                $input['lng'] = $geocode['lng'];
                $input['center'] = true;

                $address = Address::create($input);
            } else {
                $input = $request->all();
            }
            $input['address_id'] = $address['id'];

            $input['action_status_id'] = config('status.action_geplant');
            $input['user_id'] = $aktUser->id;

            $action = Action::create($input);
            $aktUser->update(['action_id' => $action->id, 'role_id' => config('status.role_actionleader')]);
            ActionUser::create([
                'user_id' => $aktUser->id,
                'action_id' => $action->id,
                'role_id' => config('status.role_actionleader'),]);
            if ($request->has('addGroupUsers')){
                Helper::AddGroupUsersToAction($group, $action);
            }
        }

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
    public function edit(Action $action)
    {
        //
        $action_statuses = ActionStatus::pluck('name', 'id')->all();
        $title = 'Aktion - Bearbeiten';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Aktionen';
        $help['main_route'] =  '/admin/actions';

        return view('admin.actions.edit', compact('action', 'action_statuses', 'title', 'help'));
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
        $aktUser = Auth::user();
        if(!$aktUser->demo) {
            $action = Action::findOrFail($id);
            $address = $action->center;
            $input = $request->all();
            GeoCoder::setApiKey($input['APIKey']);
            GeoCoder::setCountry('CH');
            $geocode = Geocoder::getCoordinatesForAddress($input['street'] . ', ' . $input['plz'] . ' ' . $input['city']);
            $input['lat'] = $geocode['lat'];
            $input['lng'] = $geocode['lng'];
            $input['center'] = true;
            if ($address) {
                $address->update($input);
            } else {
                if (!$aktUser->isAdmin()) {
                    $group = $aktUser->group;
                    $input['group_id'] = $group['id'];
                }
                $address = Address::create($input);
                $input['address_id'] = $address['id'];
            }
            $status_id = (int)$input['action_status_id'];
            if ($status_id != $action['action_status_id']) {
                if ($status_id === config('status.action_aktiv')) {
                    $input['text'] = 'Aktion ' . $action['name'] . ' wurde gestartet.';
                    NotificationCreate::dispatch($action, $input);
                }
                if ($status_id === config('status.action_abgeschlossen')) {
                    $input['text'] = 'Aktion ' . $action['name'] . ' wurde abgeschlossen.';
                    NotificationCreate::dispatch($action, $input);
                }
            }
            $action->update($input);
        }

        return redirect('/admin/actions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request, Action $action)
    {
        //
        $aktUser = Auth::user();
        if(!$aktUser->demo) {
            $input = $request->all();
            if ($action->action_status_id === config('status.action_geplant')) {
                $action->action_status_id = config('status.action_aktiv');
                $log['text'] = 'Aktion ' . $action['name'] . ' wurde gestartet.';
            } else {
                $action->action_status_id = config('status.action_abgeschlossen');

                $input['total_amount'] = $action->notifications()->sum('quantity');
                $log['text'] = 'Aktion ' . $action['name'] . ' wurde abgeschlossen.';
            }
            $log['user'] = $aktUser->username;
            NotificationCreate::dispatch($action, $log);
            $action->update($input);
        }

        return redirect('/admin/actions');
    }

    public function updateAction(Request $request, Action $action)
    {
        //
        $aktUser = Auth::user();
        if(!$aktUser->demo) {
            Helper::updateAction($aktUser, $action);
        }

        return redirect('/home');
    }

    public function destroy(Action $action)
    {
        //
        $aktUser = Auth::user();
        if(!$aktUser->demo) {
            $action->delete();
            Helper::updateAction($aktUser, $action);
        }
        return redirect('/admin/actions');
    }
}
