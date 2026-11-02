@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC9auFe3/SE0yG4aJCOd/qxew74NN7eyiSKjr7xJJMu1Jy2wf7FXITpWS1E/RY8yzuXN7VA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7ipEW6B6k+Aq20PV31bjODg4AA52W+tYtAE0jE0kMx49bjJ3FgvS56wzmyfMUHbQ4Km2b7l9+Y/+Eg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@section('content')

<x-page-title :title="$title" :help="$help" />
@if (Session::has('deleted_user'))
<p class="bg-danger">{{session('deleted_user')}}</p>
@endif
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <div class="row">
            <div class="col-sm-3">
                <p>Leiter Hinzufügen:</p>
                <form method="POST" action="{{ action('AdminUsersController@add') }}" accept-charset="UTF-8">
                    @csrf
                <div class="form-group">
                    <x-form.label for="email_add" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-Mail:</x-form.label>
                    <x-form.input name="email_add" type="email" id="email_add" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 typeahead autocomplete_txt" placeholder="name@abt" required />
                </div>
                <div class="form-group">
                    <x-form.label for="role_id_add" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rolle:</x-form.label>
                    <x-form.select name="role_id_add" :options="['' => 'Wähle Rolle'] + $roles" id="role_id_add" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                </div>
                <x-form.input name="user_id" type="hidden" class="form-control typeahead autocomplete_txt" />
                <div class="form-group">
                    <x-form.submit value="Leiter Hinzufügen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                </div>
                </form>
            </div>
            <div class="col-sm-3">
                <p>Leiter Erfassen:</p>
                <form method="POST" action="{{ action('AdminUsersController@store') }}" accept-charset="UTF-8" enctype="multipart/form-data">
                    @csrf
                <div class="form-group">
                    <x-form.label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name:</x-form.label>
                    <x-form.input name="username" id="username" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@abt" />
                </div>
                <div class="form-group">
                    <x-form.label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-Mail:</x-form.label>
                    <x-form.input name="email" type="email" id="email" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@abt" required />
                </div>

                <div class="form-group">
                    <x-form.label for="role_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role:</x-form.label>
                    <x-form.select name="role_id" :options="['' => 'Wähle Rolle'] + $roles" id="role_id" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
                
                <div class="form-group">
                    <x-form.label for="avatar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bild:</x-form.label>
                    <x-form.input name="avatar" type="file" id="avatar" class="photo block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                </div>
                <div class="form-group">
                    <x-form.input name="cropped_photo_id" type="hidden" class="form-control" id="cropped_photo_id" />
                </div>

                <div class="form-group">
                    <x-form.label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password:</x-form.label>
                    <x-form.input name="password" type="password" id="password" class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>

                <div class="form-group">
                    <x-form.submit value="Leiter Erfassen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" />
                </div>
                </form>

                @include('includes.form_error')
            </div>
            <div class="col-md-6">
                <a id="addGroupUsers" href="#" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                    <span>Alle Personen der Gruppe zur Aktion hinzufügen</span>
                </a>
                <br>
                <br>
                <table class="table table-striped table-responsive" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col" width="10%"></th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Gruppe</th>
                            <th>Rolle</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                    <button type="button" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" data-dismiss="modal">Abbrechen</button>
                    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" id="crop">Zuschneiden</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script> --}}
    @include('admin/users/photo_cropped_js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    buttons: [],
                    language: {
                        "url": "/lang/Datatables.json"
                    },
                    ajax: "{!! route('users.CreateDataTables') !!}",
                    order: [[2, 'asc']],
                    columns: [
                        {data: 'picture', name: 'picture'},
                        {data: 'username', name: 'username'},
                        {data: 'email', name: 'email'},
                        {data: 'group', name: 'group'},
                        {data: 'role', name: 'role'},
                    ]
                });
            });
           
            $('#addGroupUsers').on('click', function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                // confirm then
                $.ajax({
                    url: "{{route('users.addGroupUsers')}}",
                    type: 'POST',
                    dataType: 'json',
                    data: {method: 'POST', submit: true},
                }).always(function () {
                    $('#datatable').DataTable().draw(false);
                });
            });
            //autocomplete script
            $(document).on('focus', '.autocomplete_txt', function () {
                $(this).autocomplete({
                    minLength: 3,
                    highlight: true,
                    source: function (request, response) {
                        $.ajax({
                            url: "{{ route('searchajaxuser') }}",
                            dataType: "json",
                            data: {
                                term: request.term,
                            },
                            success: function (data) {
                                var array = $.map(data, function (item) {
                                    return {
                                        label: item['username'] + ' - ' + item['email'],
                                        value: item['email'],
                                        data: item
                                    }
                                });
                                response(array)
                            }
                        });
                    },
                    select: function (event, ui) {
                        var data = ui.item.data;
                        $("[name='email_add']").val(data.value);
                        $("[name='user_id']").val(data.id);
                    }
                });
            });
        }, false);
    </script>
@endpush