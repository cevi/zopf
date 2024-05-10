@extends('layouts.admin')

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
                {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@add']) !!}
                <div class="form-group">
                    {!! Form::label('email_add', 'E-Mail:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::email('email_add', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 typeahead autocomplete_txt', 'placeholder' => 'name@abt', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('role_id_add', 'Rolle:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('role_id_add', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'required']) !!}
                </div>
                {!! Form::hidden('user_id', null, ['class' => 'form-control typeahead autocomplete_txt']) !!}
                <div class="form-group">
                    {!! Form::submit('Leiter Hinzufügen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-3">
                <p>Leiter Erfassen:</p>
                {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@store']) !!}
                <div class="form-group">
                    {!! Form::label('username', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::text('username', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'placeholder' => 'name@abt']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'E-Mail:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::email('email', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500', 'placeholder' => 'name@abt', 'required'])
                    !!}
                </div>

                <div class="form-group">
                    {!! Form::label('role_id', 'Role:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('password', 'Password:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::password('password', ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Leiter Erfassen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}

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
                            {{-- <th></th> --}}
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Gruppe</th>
                            <th>Rolle</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script> --}}
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
                    columns: [
                        //    {data: 'checkbox', name: 'checkbox', orderable:false,serachable:false,sClass:'text-center'},
                        {data: 'username', name: 'username'},
                        {data: 'email', name: 'email'},
                        {data: 'group', name: 'group'},
                        {data: 'role', name: 'role'},
                        {data: 'Actions', name: 'Actions', orderable: false, serachable: false, sClass: 'text-center'},

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