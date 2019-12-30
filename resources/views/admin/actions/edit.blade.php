@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/actions">Aktionen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Aktionen</h1>
            </header>
            <div class="row">
    
                <div class="col-sm-6">
                    {!! Form::model($action, ['method' => 'Patch', 'action'=>['AdminActionsController@update',$action->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('year', 'Jahr:') !!}
                            {!! Form::text('year', null, ['class' => 'form-control']) !!}
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
            </div>
        </div>  
    </section> 
 

@endsection