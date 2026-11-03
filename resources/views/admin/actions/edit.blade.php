@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">

                <div class="col-sm-6">
                    <form method="POST" action="{{ action('AdminActionsController@update', $action->id) }}" accept-charset="UTF-8">
                        @csrf
                        @method('PATCH')
                    <div class="form-group">
                        <x-form.label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                        <x-form.input name="name" :model="$action" id="name" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="form-group">
                        <x-form.label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jahr:</x-form.label>
                        <x-form.input name="year" :model="$action" id="year" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="form-group">
                        <x-form.label for="street" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Strasse:</x-form.label>
                        <x-form.input name="street" :value="$action->center['street']" :model="$action" id="street" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <x-form.label for="plz" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PLZ:</x-form.label>
                            <x-form.input name="plz" :value="$action->center['plz']" :model="$action" id="plz" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        <div class="form-group col-md-9">
                            <x-form.label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ort:</x-form.label>
                            <x-form.input name="city" :value="$action->center['city']" :model="$action" id="city" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="form-group">
                        <x-form.label for="APIKey" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Google API Key:</x-form.label>
                        <x-form.input name="APIKey" :model="$action" id="APIKey" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="form-group">
                        <x-form.label for="action_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status:</x-form.label>
                        <x-form.select name="action_status_id" :options="$action_statuses" :model="$action" id="action_status_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div class="form-group">
                        <x-form.submit value="Aktion aktualisieren" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>

                    <form method="POST" action="{{ action('AdminActionsController@destroy', $action) }}" accept-charset="UTF-8">
                        @csrf
                        @method('DELETE')
                    <div class="form-group">
                        <x-form.submit value="Aktion löschen" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" />
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
