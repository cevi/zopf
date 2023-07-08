@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
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
                        {!! Form::submit('Route Erfassen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </section>
@endsection
