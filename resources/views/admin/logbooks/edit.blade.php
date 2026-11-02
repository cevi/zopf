@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">

                <div class="col-sm-6">
                    <form method="POST" action="{{ action('AdminLogbookController@update', $notification) }}" accept-charset="UTF-8">
                        @csrf
                        @method('PATCH')
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <x-form.label for="when" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Wann:</x-form.label>
                            <x-form.input name="when" type="time" :model="$notification" id="when" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                        </div>
                        <div class="form-group col-md-6">
                            <x-form.label for="user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Verantwortlicher:</x-form.label>
                            <x-form.input name="user" :model="$notification" id="user" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        <div class="form-group col-md-3">
                            <x-form.label for="cut" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anzahl Aufgeschnitten:</x-form.label>
                            <x-form.input name="cut" :model="$notification" id="cut" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="form-group">
                        <x-form.label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kommentar:</x-form.label>
                        <x-form.input name="content" :model="$notification" id="content" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div class="form-group">
                        <x-form.submit value="Eintrag Aktualisieren" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>
                    <form method="POST" action="{{ action('AdminLogbookController@destroy', $notification) }}" accept-charset="UTF-8">
                        @csrf
                        @method('DELETE')
                    <div class="form-group">
                        <x-form.submit value="Eintrag löschen" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" />
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
