@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    <form method="POST" action="{{ action('AdminOrdersController@update', $order->id) }}" accept-charset="UTF-8">
                        @csrf
                        @method('PATCH')
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <x-form.label for="firstname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vorname:</x-form.label>
                            <x-form.input name="firstname" :value="$order->address['firstname']" :model="$order" id="firstname" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                        </div>
                        <div class="form-group col-md-6">
                            <x-form.label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                            <x-form.input name="name" :value="$order->address['name']" :model="$order" id="name" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="form-group">
                        <x-form.checkbox name="pick_up" value="yes" :checked="$order['pick_up']" :model="$order" id="pick_up" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                        <x-form.label for="pick_up" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Abholen</x-form.label>
                    </div>

                    <div class="form-group">
                        <x-form.label for="street" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Strasse:</x-form.label>
                        <x-form.input name="street" :value="$order->address['street']" :model="$order" id="street" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <x-form.label for="plz" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PLZ:</x-form.label>
                            <x-form.input name="plz" :value="$order->address['plz']" :model="$order" id="plz" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        <div class="form-group col-md-9">
                            <x-form.label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ort:</x-form.label>
                            <x-form.input name="city" :value="$order->address['city']" :model="$order" id="city" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>

                    <div class="form-group">
                        <x-form.label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anzahl:</x-form.label>
                        <x-form.input name="quantity" :model="$order" id="quantity" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="route_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Route:</x-form.label>
                        <x-form.select name="route_id" :options="$routes" :model="$order" id="route_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="order_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status:</x-form.label>
                        <x-form.select name="order_status_id" :options="$order_statuses" :model="$order" id="order_status_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="comments" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bemerkung:</x-form.label>
                        <x-form.input name="comments" :model="$order" id="comments" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.submit value="Bestellung Aktualisieren" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>

                    <form method="POST" action="{{ action('AdminOrdersController@destroy', $order) }}" accept-charset="UTF-8">
                        @csrf
                        @method('DELETE')
                    <div class="form-group">
                        <x-form.submit value="Bestellung löschen" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" />
                    </div>
                    </form>
                </div>
            </div>
    </section>
@endsection
