@extends('layouts.admin')
@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/actions">Aktionen</a></li>
            <li class="breadcrumb-item active">Erfassen</li>
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
                </div>
            </div>
        </div>
    </section>
@endsection