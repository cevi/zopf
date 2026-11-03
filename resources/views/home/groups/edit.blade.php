@extends('layouts.layout')
@section('content')
<x-page-title :title="$title" :help="$help" :header=false/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-3">
                    <form method="POST" action="{{ action('GroupsController@update', $group) }}" accept-charset="UTF-8">
                        @csrf
                        @method('PATCH')
                    <div class="form-group">      
                        <x-form.label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                        <x-form.input name="name" :model="$group" id="name" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div class="form-group">
                        <x-form.submit value="Gruppe Aktualisieren" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
