@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminActionsController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('year', 'Jahr:') !!}
                        {!! Form::text('year', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:') !!}
                        {!! Form::text('street', null, ['class' => 'form-control ']) !!}
                        {!! Form::hidden('address_id', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('plz', 'PLZ:') !!}
                            {!! Form::text('plz', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:') !!}
                            {!! Form::text('city', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('APIKey', 'Google API Key:') !!}
                        {!! Form::text('APIKey', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    @if (Auth::user()->isAdmin())
                        <div class="form-group">
                            {!! Form::label('group_id', 'Gruppe:') !!}
                            {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'form-control']) !!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::label('addGroupUsers', 'Personen der Gruppe auf Aktion übernehmen:') !!}
                        {!! Form::checkbox('addGroupUsers', '1', false) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Aktion Erfassen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
@endsection
