@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Routen</h1>
            </header>
            <div class="row">
    
                <div class="col-sm-6">
                    {!! Form::model($route, ['method' => 'Patch', 'action'=>['AdminRoutesController@update',$route->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('user_id', 'Verantwortlicher:') !!}
                            {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('route_status_id', 'Rolle:') !!}
                            {!! Form::select('route_status_id', $route_statuses, null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Update Aktion', ['class' => 'btn btn-primary'])!!}
                        </div>
                    {!! Form::close()!!}

                    {!! Form::model($route, ['method' => 'DELETE', 'action'=>['AdminRoutesController@destroy',$route->id]]) !!}
                        <div class="form-group">
                            {!! Form::submit('Route löschen', ['class' => 'btn btn-danger'])!!}
                        </div>
                    {!! Form::close()!!}
                </div>  
            </div>
        </div>  
    </section> 
 

@endsection