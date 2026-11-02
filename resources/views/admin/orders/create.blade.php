@extends('layouts.admin')
@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <div class="row">
            <div class="col-sm-6">
                @include('includes.form_error')
                <form method="POST" action="{{ action('AdminOrdersController@store') }}" accept-charset="UTF-8">
                    @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-form.label for="firstname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vorname:</x-form.label>
                        <x-form.input name="firstname" id="firstname" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                    </div>
                    <div class="form-group col-md-6">
                        <x-form.label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                        <x-form.input name="name" id="name" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <x-form.input name="address_id" type="hidden" class="form-control" />
                </div>
                <div class="form-group">
                    <x-form.checkbox name="pick_up" value="yes" :checked="false" id="pick_up" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    <x-form.label for="pick_up" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Abholen</x-form.label> 
                </div>
                <div class="address">
                    <div class="form-group">
                        <x-form.label for="street" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Strasse:</x-form.label>
                        <x-form.input name="street" id="street" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
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
                </div>

                <div class="form-group">
                    <x-form.label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anzahl:</x-form.label>
                    <x-form.input name="quantity" id="quantity" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                </div>

                <div class="form-group">
                    <x-form.label for="routes_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Route:</x-form.label>
                    <x-form.select name="routes_id" :options="['' => 'Wähle Route'] + $routes" id="routes_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>

                <div class="form-group">
                    <x-form.label for="comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bemerkung:</x-form.label>
                    <x-form.input name="comment" id="comment" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>

                <div class="form-group">
                    <x-form.submit value="Adresse Erfassen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                </div>
                </form>
            </div>
        </div>
</section>
@endsection


@push('scripts')
    <script type="module">
        $('input[type="checkbox"]').click(function () {
            $(".address").toggle();
        });
    </script>
@endpush