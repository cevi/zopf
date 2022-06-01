@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Logbuch-Eintrag bearbeiten</h1>
            </header>
            <div class="row">

                <div class="col-sm-6">
                    {!! Form::model($logbook, ['method' => 'Patch', 'action'=>['AdminLogbookController@update',$logbook]]) !!}
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                {!! Form::label('wann', 'Wann:') !!}
                                {!! Form::time('wann',null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('user_id', 'Verantwortlicher:') !!}
                                {!! Form::select('user_id', [''=>'Wähle Leiter'] + $users, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-3">
                                {!! Form::label('cut', 'Anzahl Aufgeschnitten:') !!}
                                {!! Form::text('cut', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('comments', 'Kommentar:') !!}
                            {!! Form::text('comments', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Eintrag Aktualisieren', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}
                    {!! Form::model($logbook, ['method' => 'DELETE', 'action'=>['AdminLogbookController@destroy',$logbook]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Eintrag löschen', ['class' => 'btn btn-danger'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </section>


@endsection
