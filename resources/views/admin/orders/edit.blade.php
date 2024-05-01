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
                            {!! Form::label('firstname', 'Vorname:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('firstname', $order->address['firstname'], ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('name', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('name', $order->address['name'], ['class' =>'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('pick_up', 'yes', $order['pick_up'], ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                        {!! Form::label('pick_up', 'Abholen', ['class' =>'mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('street', $order->address['street'], ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('plz', 'PLZ:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('plz', $order->address['plz'], ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('city', $order->address['city'], ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Anzahl:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('quantity', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('route_id', 'Route:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::select('route_id', $routes, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('order_status_id', 'Status:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::select('order_status_id', $order_statuses, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('comments', 'Bemerkung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('comments', null, ['class' =>'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Bestellung Aktualisieren', ['class' =>  'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                    {!! Form::close()!!}

                    {!! Form::model($order, ['method' => 'DELETE', 'action'=>['AdminOrdersController@destroy',$order]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Bestellung löschen', ['class' => 'focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
    </section>
@endsection
