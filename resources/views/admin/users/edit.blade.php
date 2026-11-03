@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC9auFe3/SE0yG4aJCOd/qxew74NN7eyiSKjr7xJJMu1Jy2wf7FXITpWS1E/RY8yzuXN7VA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7ipEW6B6k+Aq20PV31bjODg4AA52W+tYtAE0jE0kMx49bjJ3FgvS56wzmyfMUHbQ4Km2b7l9+Y/+Eg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">

                <div class="col-sm-6">

                    <form method="POST" action="{{ action('AdminUsersController@update', $user->id) }}" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                    <div class="form-group">
                        <x-form.label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                        <x-form.input name="username" :model="$user" id="username" required class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-Mail:</x-form.label>
                        <x-form.input name="email" type="email" :model="$user" id="email" required class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="role_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rolle:</x-form.label>
                        <x-form.select name="role_id" :options="$roles" :model="$user" id="role_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>
                    <div class="form-group">
                        <x-form.label for="avatar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bild:</x-form.label>
                        <x-form.input name="avatar" type="file" id="avatar" class="photo block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                    </div>
                    <div class="form-group">
                        <x-form.input name="cropped_photo_id" type="hidden" :model="$user" class="form-control" id="cropped_photo_id" />
                    </div>

                    @if ((Auth::user()->isAdmin()))
                        <div class="form-group">
                            <x-form.label for="group_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gruppe:</x-form.label>
                            <x-form.select name="group_id" :options="['' => 'Wähle Gruppe'] + $groups" :model="$user" id="group_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    @endif

                    <div class="form-group">
                        <x-form.label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status:</x-form.label>
                        <x-form.select name="is_active" :options="array(1 => 'Aktiv', 0 => 'Nicht Aktiv')" :model="$user" id="is_active" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </div>

                    <div class="form-group">
                        <x-form.label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password:</x-form.label>
                        <x-form.input name="password" type="password" id="password" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />

                    <div class="form-group">
                        <x-form.submit value="Update Leiter" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                    </div>
                    </form>

                    <form method="POST" action="{{ action('AdminUsersController@destroy', $user) }}" accept-charset="UTF-8">
                        @csrf
                        @method('DELETE')
                    <div class="form-group">
                        <x-form.submit value="Lösche Leiter" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" />
                    </div>
                    </form>
                </div>
            </div>
            <div class="row">
                @include('includes.form_error')
            </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Vorschaubild zuschneiden</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <div class="row">
                                <div class="col-md-8">
                                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                                </div>
                                <div class="col-md-4">
                                    <div class="preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-primary" id="crop">Zuschneiden</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @include('admin/users/photo_cropped_js')
@endpush