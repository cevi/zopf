@extends('layouts.admin')

@section('content')

    <x-page-title :title="$title" :help="$help"/>
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
                        {!! Form::label('email_add', 'E-Mail:') !!}
                        {!! Form::email('email_add', null, ['class' => 'form-control typeahead autocomplete_txt', 'placeholder' => 'name@abt', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('role_id_add', 'Rolle:') !!}
                        {!! Form::select('role_id_add', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    {!! Form::hidden('user_id', null, ['class' => 'form-control typeahead autocomplete_txt']) !!}
                    <div class="form-group">
                        {!! Form::submit('Leiter Hinzufügen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-sm-3">
                    <p>Leiter Erfassen:</p>
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminUsersController@store']) !!}
                    <div class="form-group">
                        {!! Form::label('username', 'Name:') !!}
                        {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'name@abt']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', 'E-Mail:') !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'name@abt', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('role_id', 'Role:') !!}
                        {!! Form::select('role_id', [''=>'Wähle Rolle'] + $roles, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('password', 'Password:') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Leiter Erfassen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    @include('includes.form_error')
                </div>
                <div class="col-md-6">
                    <a id="addGroupUsers" href="#" class="btn btn-primary">
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

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    {{--    <script--}}
    {{--        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>--}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
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
            $('#datatable').on('click', '.btn-danger[data-remote]', function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var url = $(this).data('remote');
                // confirm then
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: {method: 'DELETE', submit: true}
                }).always(function (data) {
                    $('#datatable').DataTable().draw(false);
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
                        console.log(ui);
                        console.log(event);
                        var data = ui.item.data;
                        $("[name='email_add']").val(data.value);
                        $("[name='user_id']").val(data.id);
                    }
                });
            });
        }, false);
    </script>
@endsection
