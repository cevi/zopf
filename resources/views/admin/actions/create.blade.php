@extends('layouts.admin')
@section('content')
<h1>Erstelle Aktion</h1>
    {!! Form::open(['method' => 'POST', 'action'=>'AdminActionsController@store']) !!}
    <div class="form-group">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('year', 'Jahr:') !!}
        {!! Form::text('year', null, ['class' => 'form-control']) !!}
    </div>


    @if (Auth::user()->isAdmin())
        <div class="form-group">
            {!! Form::label('group_id', 'Gruppe:') !!}
            {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'form-control']) !!}
        </div>
    @endif

    <div class="form-group">
        {!! Form::submit('Aktion Erstellen', ['class' => 'btn btn-primary'])!!}
    </div>
    {!! Form::close()!!}

    @include('includes.form_error')
@endsection