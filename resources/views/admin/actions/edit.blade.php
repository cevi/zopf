@extends('layouts.admin')

@section('content')

    <h1>Aktion</h1>
    <div class="col-sm-6">
        {!! Form::model($action, ['method' => 'Patch', 'action'=>['AdminActionsController@update',$action->id]]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('action_status_id', 'Rolle:') !!}
                {!! Form::select('action_status_id', $action_statuses, null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Update Aktion', ['class' => 'btn btn-primary'])!!}
            </div>
         {!! Form::close()!!}

         {!! Form::model($action, ['method' => 'DELETE', 'action'=>['AdminActionsController@destroy',$action->id]]) !!}
         <div class="form-group">
             {!! Form::submit('Aktion löschen', ['class' => 'btn btn-danger'])!!}
         </div>
      {!! Form::close()!!}
    </div>    
 

@endsection