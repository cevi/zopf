@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminActionsController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('name', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('year', 'Jahr:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('year', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}                    
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('street', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        {!! Form::hidden('address_id', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            {!! Form::label('plz', 'PLZ:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('plz', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::text('city', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('APIKey', 'Google API Key:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('APIKey', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    @if (Auth::user()->isAdmin())
                        <div class="form-group">
                            {!! Form::label('group_id', 'Gruppe:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                            {!! Form::select('group_id', [''=>'Wähle Gruppe'] + $groups, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::checkbox('addGroupUsers', 'yes', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                        {!! Form::label('addGroupUsers', 'Personen der Gruppe auf Aktion übernehmen', ['class' =>'mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!} 
                    <div class="form-group">
                        {!! Form::submit('Aktion Erfassen', ['class' =>  'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                    {!! Form::close()!!}

                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
@endsection
