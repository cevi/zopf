@extends('layouts.admin')

@section('content')
    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Rückmeldungen</h5>
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
        {!! Form::open(['method' => 'POST', 'action'=>'FeedbackController@store']) !!}
        <div class="form-group">
            {!! Form::label('feedback', 'Rückmeldung:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
            {!! Form::textarea('feedback', null, ['class' =>'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required', 'rows' => 3]) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Rückmeldung absenden', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
        </div>
        {!! Form::close()!!}
    </section>

    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Änderungen
            und Anpassungen</h5>
        <ol class="relative border-l border-gray-200 dark:border-gray-700">
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V3.0</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Benachrichtungen mit Pusher <br>
                    Backstuben Verlauf mit Graph <br>
                    Verschiedene Zopfaktionen mit Gruppenzuweisungen
                </p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V2.0</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    Framework-Wechsel auf Laravel</p>
            </li>
            <li class="mb-10 ml-4">
                <div
                    class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">V1.0</h3>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">Erstellen einer Zopfaktion <br>
                    Erfassen von Adressen und Routen mit Google Maps API <br>
                    Routen an Leitende zuweisen</p>
            </li>
        </ol>
    </section>
    <section
        class="section-features block p-6 bg-white border border-gray-200 rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Geplante Änderungen</h5>
        <ol class="relative border-l border-gray-200 dark:border-gray-700">

        </ol>
    </section>

@endsection
