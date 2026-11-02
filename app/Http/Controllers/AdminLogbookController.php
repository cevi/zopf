<?php

namespace App\Http\Controllers;

use App\Models\Help;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminLogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $action = Auth::user()->action;
        $notifications = $action->Notifications()->orderBy('when', 'DESC')->get(['id', 'user', 'when', 'content'])->toArray();

        return $notifications;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $title = 'Logbuch-Eintrag - Bearbeiten';
        $notification = $this->findOwn($id);
        $help = Help::where('title', $title)->first();

        return view('admin.logbooks.edit', compact('notification', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $notification = $this->findOwn($id);
        if (! Auth::user()->demo) {
            // Nur die Felder des Formulars (sonst liesse sich z. B. notifiable_id auf eine fremde Aktion setzen).
            $notification->update($request->only(['content', 'user', 'when', 'cut']));
        }

        return redirect('/admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //

        $notification = $this->findOwn($id);
        if (! Auth::user()->demo) {
            $notification->delete();
        }

        return redirect('/admin');
    }

    /**
     * Logbuch-Eintrag der aktuellen Aktion des Benutzers, sonst 404.
     */
    private function findOwn(string $id)
    {
        $notification = Auth::user()->action?->notifications()->find($id);
        abort_if(! $notification, 404);

        return $notification;
    }
}
