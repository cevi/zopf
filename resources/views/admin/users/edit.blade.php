@extends('layouts.admin')
@section('content')
<h1>Leiter bearbeiten</h1>
    <div class="row">      

        <div class="col-sm-9">

            {!! Form::model($user, ['method' => 'PATCH', 'action'=>['AdminUsersController@update' , $user->id]]) !!}
            <div class="form-group">
                {!! Form::label('username', 'Name:') !!}
                {!! Form::text('username', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
            {!! Form::label('role_id', 'Rolle:') !!}
            {!! Form::select('role_id', $roles, null, ['class' => 'form-control']) !!}
            </div>

            @if ((Auth::user()->isAdmin()))
                <div class="form-group">
                    {!! Form::label('group_id', 'Gruppe:') !!}
                    {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'form-control']) !!}
                </div>
             @endif

            <div class="form-group">
            {!! Form::label('is_active', 'Status:') !!}
            {!! Form::select('is_active', array(1 => "Aktiv", 0 => 'Nicht Aktiv'), null,  ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
            {!! Form::label('password', 'Password:') !!}
            {!! Form::password('password', ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::submit('Update Leiter', ['class' => 'btn btn-primary'])!!}
            </div>
            {!! Form::close()!!}

            {!! Form::open(['method' => 'DELETE', 'action'=>['AdminUsersController@destroy', $user->id]]) !!}
            <div class="form-group">
             {!! Form::submit('Lösche Leiter', ['class' => 'btn btn-danger'])!!}
            </div>
             {!! Form::close()!!}
        </div>
    </div>
    <div class="row">
        @include('includes.form_error')
    </div>
@endsection