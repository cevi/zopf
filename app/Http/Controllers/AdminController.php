<?php

namespace App\Http\Controllers;

use App\Charts\TimeChart;
use App\Charts\ZopfChart;
use App\Events\NotificationCreate;
use App\Helper\Helper;
use App\Models\Action;
use App\Models\Logbook;
use App\Models\Route;
use App\Models\User;
use App\Notifications\CreateLogEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Notification;

class AdminController extends Controller
{
    //
    public function index()
    {
        $action = Auth::user()->action;
        $title = 'Dashboard';
        if ($action) {
            $notifications = $action->notifications()->get(['id', 'user', 'when', 'content'])->sortByDesc('when')->toArray();

            $open_routes = Route::where('action_id', $action['id'])->where('route_status_id', '<=', config('status.route_unterwegs'))->get()->sortByDesc('route_status_id');

            $group = Auth::user()->group;
            $users = User::where('group_id', $group['id'])->get();
            $users = $users->pluck('username', 'id')->all();
        } else {
            $open_routes = [];
            $notifications = [];
            $users = 0;
        }

        $data = Helper::GetZopfChartData($action);
        $icon_array = Helper::GetIconArray($data);

        $zopfChart = new ZopfChart;
        $zopfChart_api = url('api/action/'.$action['id'].'/zopfChart');
        $zopfChart->labels(['Offen', 'Unterwegs', 'Abholen', 'Aufgeschnitten', 'Abgeschlossen'])->load($zopfChart_api);

        $timeChart = new TimeChart;
        $timeChart_api = url('api/action/'.$action['id'].'/timeChart');
        $timeChart->labels(Helper::GetTimeChartData($action,true))->load($timeChart_api);


        return view('admin/index', compact('icon_array', 'open_routes', 'users', 'title', 'timeChart', 'zopfChart', 'notifications'));
    }

    public function logcreate(Request $request)
    {
        $action = Auth::user()->action;
        if ($action && !Auth::user()->demo) {
            $input = $request->all();
            if (! $input['user_id']) {
                $input['user_id'] = Auth::user()->id;
            }
            if (! $input['comments'] && $input['cut'] > 0) {
                $text = $input['cut'] === '1' ? ' Zopf wurde ' : ' Zöpfe wurden ';
                $input['comments'] = $input['cut'].$text.'aufgeschnitten.';
                $input['quantity'] = $input['cut'];
                $input['cut'] = true;
            } else {
                $input['cut'] = false;
                $input['quantity'] = 0;
            }
            $user = User::find($input['user_id']);
            $input['user'] = $user['username'];
            $input['text'] = $input['comments'];

            NotificationCreate::dispatch($action, $input);
        }

        return redirect('admin/');
    }

    public function changes()
    {
        $user = Auth::user();
        $title = 'Rückmeldungen / Änderungen';

        return view('admin/changes', compact('user', 'title'));
    }

    public function notifications_read()
    {
        Auth::user()->action->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function timeChart(Action $action)
    {
        $timeChart = new TimeChart;

        $timeChart->minimalist(true);
        $timeChart->labels(Helper::GetTimeChartData($action,true));
        $timeChart->dataset('Anzahl Zöpfe', 'line', Helper::GetTimeChartData($action))
            ->color(['#4f92c7'])
            ->backgroundColor(['#a8d0f0']);

        return $timeChart->api();
    }

    public function zopfChart(Action $action)
    {
        $data = Helper::GetZopfChartData($action);

        $zopfChart = new ZopfChart;

        $zopfChart->minimalist(true);
        $zopfChart->dataset('Zöpfe', 'doughnut', [$data['orders_open'], $data['orders_delivery'], $data['orders_open_pickup'], $data['cut'], $data['orders_finished']])
            ->color(['#4f92c7', '#21d19f', '#522a27', '#c73e1d', '#c59849'])
            ->backgroundColor(['#4f92c7', '#21d19f', '#522a27', '#c73e1d', '#c59849']);

        return $zopfChart->api();
    }

    public function GetIconArray(Request $request)
    {
        $input = $request->all();
        $action = Action::findOrFail($input['action']['id']);

        $data = Helper::GetZopfChartData($action);
        $icon_array = Helper::GetIconArray($data);
        return $icon_array;

    }


}
