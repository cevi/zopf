@extends('layouts.admin')

@section('content')
<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <br>
        @if(count($progress)>1)
        <div class="area-chart">
            {!! $progressChart->container() !!}
        </div>
        <br>
        @endif
        <form method="POST" action="{{ action('AdminBakeryProgressController@store') }}" accept-charset="UTF-8">
            @csrf
        <div class="row">
            <div class="form-group col-xl-1 col-6">
                <x-form.label for="when" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Wann:</x-form.label>
                <x-form.input name="when" type="time" :value="now()" id="when" required class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-2 col-6">
                <x-form.label for="raw_material" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Roh-Materialien:</x-form.label>
                <x-form.input name="raw_material" type="number" id="raw_material" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-2 col-6">
                <x-form.label for="dough" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teig:</x-form.label>
                <x-form.input name="dough" type="number" id="dough" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-2 col-6">
                <x-form.label for="braided" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gezöpfelt:</x-form.label>
                <x-form.input name="braided" type="number" id="braided" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-2 col-6">
                <x-form.label for="baked" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gebacken:</x-form.label>
                <x-form.input name="baked" type="number" id="baked" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-2 col-6">
                <x-form.label for="delivered" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ausgeliefert:</x-form.label>
                <x-form.input name="delivered" type="number" id="delivered" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
            <div class="form-group col-xl-1 col-2">
                <br>
                <x-form.submit value="Eintrag Erfassen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
            </div>
        </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Wann</th>
                    <th scope="col">Roh-Materialien</th>
                    <th scope="col">Teig</th>
                    <th scope="col">Gezöpfelt</th>
                    <th scope="col">Gebacken</th>
                    <th scope="col">Ausgeliefert</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($progress as $act_progress)
                <tr>
                    <td><a href="{{route('progress.edit', $act_progress)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$act_progress['when']}}</a></td>
                    <td>{{$act_progress['raw_material']}}</td>
                    <td>{{$act_progress['dough']}}</td>
                    <td>{{$act_progress['braided']}}</td>
                    <td>{{$act_progress['baked']}}</td>
                    <td>{{$act_progress['delivered']}}</td>
                    <td>{{$act_progress['total']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
{{isset($progressChart) ? $progressChart->script() : ''}}

@endpush