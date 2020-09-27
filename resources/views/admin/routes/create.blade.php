@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
            </ul>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Route erfassen</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminRoutesController@store']) !!}
                    <div class="form-row">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('user_id', 'Verantwortlicher:') !!}
                        {!! Form::select('user_id', [''=>'Wähle Leiter'] + $users, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('route_type_id', 'Routen Art:') !!}
                        {!! Form::select('route_type_id', [''=>'Wähle Routen Art'] + $route_types, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Route Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </section>
@endsection