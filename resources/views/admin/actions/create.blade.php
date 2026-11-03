@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <form method="POST" action="{{ action('AdminActionsController@store') }}" accept-charset="UTF-8">
                        @csrf
                    <div class="form-group">
                        <x-form.label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                        <x-form.input name="name" id="name" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>

                    <div class="form-group">
                        <x-form.label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jahr:</x-form.label>
                        <x-form.input name="year" id="year" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />                    
                    </div>

                    <div class="form-group">
                        <x-form.label for="street" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Strasse:</x-form.label>
                        <x-form.input name="street" id="street" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        <x-form.input name="address_id" type="hidden" class="form-control" />
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <x-form.label for="plz" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PLZ:</x-form.label>
                            <x-form.input name="plz" id="plz" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        <div class="form-group col-md-9">
                            <x-form.label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ort:</x-form.label>
                            <x-form.input name="city" id="city" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="form-group">
                        <x-form.label for="APIKey" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Google API Key:</x-form.label>
                        <x-form.input name="APIKey" id="APIKey" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    @if (Auth::user()->isAdmin())
                        <div class="form-group">
                            <x-form.label for="group_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gruppe:</x-form.label>
                            <x-form.select name="group_id" :options="['' => 'Wähle Gruppe'] + $groups" id="group_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    @endif
                    <div class="form-group">
                        <x-form.checkbox name="addGroupUsers" value="yes" :checked="false" id="addGroupUsers" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                        <x-form.label for="addGroupUsers" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Personen der Gruppe auf Aktion übernehmen</x-form.label> 
                    <div class="form-group">
                        <x-form.submit value="Aktion Erfassen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>

                    @include('includes.form_error')
                </div>
            </div>
        </div>
    </section>
@endsection
