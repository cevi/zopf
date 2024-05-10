@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-3">
                @if (session()->has('success'))
                    <div class="alert alert-dismissable alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>
                            {!! session()->get('success') !!}
                        </strong>
                    </div>
                @endif
                {!! Form::open(['action'=>'FeedbackController@send']) !!}
                    <div class="form-group">
                        {!! Form::label('title', 'Titel:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::text('title', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Beschreibung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                        {!! Form::textarea('description', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 10]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('bug', 'yes', false, ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']) !!}
                        {!! Form::label('bug', 'Bug', ['class' =>'mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!} 

                    </div>
                    <div class="form-group">
                        {!! Form::submit('Issue auf Github erstellen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                    </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-9">
                @if ($feedbacks)
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Feedback</th>
                                <th scope="col">Von</th>
                            </tr>
                        </thead>
                        @foreach ($feedbacks as $feedback)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('feedback.edit',$feedback)}}">{{$feedback->feedback}}</a></td>
                                    <td>{{$feedback->user['username']}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
