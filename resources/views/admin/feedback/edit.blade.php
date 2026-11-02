@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header-->
        <x-page-title :title="$title" :help="$help"/>
        <div class="row">
            <div class="col-sm-6">
                <form method="POST" action="{{ action('FeedbackController@update', $feedback) }}" accept-charset="UTF-8">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <x-form.label for="feedback" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Feedback:</x-form.label>
                        <x-form.textarea name="feedback" :model="$feedback" id="feedback" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required rows="10" />
                    </div>
                    <div class="form-group">
                        <x-form.submit value="Änderungen speichern" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                </form>

                </div>
        </div>
    </div>
@endsection
