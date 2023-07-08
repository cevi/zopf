@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">

                <div class="col-sm-6">
                    {!! Form::model($notification, ['method' => 'Patch', 'action'=>['AdminLogbookController@update',$notification]]) !!}
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('when', 'Wann:') !!}
                            {!! Form::time('when',null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('user', 'Verantwortlicher:') !!}
                            {!! Form::text('user', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            {!! Form::label('cut', 'Anzahl Aufgeschnitten:') !!}
                            {!! Form::text('cut', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('content', 'Kommentar:') !!}
                        {!! Form::text('content', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Eintrag Aktualisieren', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                    {!! Form::model($notification, ['method' => 'DELETE', 'action'=>['AdminLogbookController@destroy',$notification]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Eintrag löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </section>

@endsection
