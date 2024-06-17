@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                <li class="breadcrumb-item active">Hilfe-Artikel</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Hilfe-Artikel</h1>
            </header>
            <div class="row">
                <div class="col-sm-4">
                    <p>Artikel Erfassen:</p>
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminHelpController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('title', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('content', 'Inhalt:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::textarea('content', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 10]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Artikel Erfassen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-md-8">
                    <table class="table table-striped table-responsive" id="datatable">
                        <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th>Titel</th>
                            <th>Inhalt</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($helps as $help)
                            <tr>
                                {{-- <th></th> --}}
                                <td><a href="{{route('helps.edit', $help)}}">{{$help['title']}}</a></td>
                                <td>{!! nl2br($help['content']) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
