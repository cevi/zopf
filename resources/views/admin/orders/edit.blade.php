@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::model($order, ['method' => 'Patch', 'action'=>['AdminOrdersController@update',$order->id]]) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('firstname', 'Vorname:') !!}
                            {!! Form::text('firstname', $order->address['firstname'], ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', $order->address['name'], ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('pick_up', 'Abholen:') !!}
                        {!! Form::checkbox('pick_up', '1',  $order['pick_up']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:') !!}
                        {!! Form::text('street', $order->address['street'], ['class' => 'form-control ']) !!}
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('plz', 'PLZ:') !!}
                            {!! Form::text('plz', $order->address['plz'], ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:') !!}
                            {!! Form::text('city', $order->address['city'], ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Anzahl:') !!}
                        {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('route_id', 'Route:') !!}
                        {!! Form::select('route_id', $routes, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('order_status_id', 'Status:') !!}
                        {!! Form::select('order_status_id', $order_statuses, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('comments', 'Bemerkung:') !!}
                        {!! Form::text('comments', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Bestellung Aktualisieren', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    {!! Form::model($order, ['method' => 'DELETE', 'action'=>['AdminOrdersController@destroy',$order]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Bestellung löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
    </section>
@endsection
