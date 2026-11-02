@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <div class="col-sm-6">
                <form method="POST" action="{{ action('AdminBakeryProgressController@update', $progress) }}" accept-charset="UTF-8">
                    @csrf
                    @method('PATCH')
                <div class="form-group">
                    <x-form.label for="when" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Wann:</x-form.label>
                    <x-form.input name="when" type="time" :model="$progress" id="when" required class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.label for="raw_material" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Roh-Materialien:</x-form.label>
                    <x-form.input name="raw_material" type="number" :model="$progress" id="raw_material" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.label for="dough" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teig:</x-form.label>
                    <x-form.input name="dough" type="number" :model="$progress" id="dough" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.label for="braided" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gezöpfelt:</x-form.label>
                    <x-form.input name="braided" type="number" :model="$progress" id="braided" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.label for="baked" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gebacken:</x-form.label>
                    <x-form.input name="baked" type="number" :model="$progress" id="baked" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.label for="delivered" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ausgeliefert:</x-form.label>
                    <x-form.input name="delivered" type="number" :model="$progress" id="delivered" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                <div class="form-group">
                    <x-form.submit value="Eintrag bearbeiten" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                </div>
                </form>

                <form method="POST" action="{{ action('AdminBakeryProgressController@destroy', $progress) }}" accept-charset="UTF-8">
                    @csrf
                    @method('DELETE')
                <div class="form-group">
                    <x-form.submit value="Eintrag löschen" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" />
                </div>
                </form>
            </div>
        </div>

    </section>
@endsection
