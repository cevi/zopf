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
            {!! Form::label('feedback', 'Rückmeldung:') !!}
            {!! Form::textarea('feedback', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Rückmeldung absenden', ['class' => 'btn btn-primary'])!!}
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
