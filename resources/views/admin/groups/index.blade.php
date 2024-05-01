@extends('layouts.admin')

@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <div class="row">
            <div class="col-sm-3">
                {!! Form::open(['method' => 'POST', 'action'=>'AdminGroupsController@store']) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('user_id', 'Gruppenleiter:') !!}
                    {!! Form::select('user_id',[''=>'Bitte wählen'] + $users, null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Gruppe Erfassen', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-9">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Gruppenleiter</th>
                            <th scope="col">Aktionen</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    language: {
                        "url": "/lang/Datatables.json"
                    },
                    ajax: {
                        "url": "{!! route('groups.CreateDataTables') !!}"
                    },
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'groupleader', name: 'groupleader'},
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

            $.fn.dataTable.ext.errMode = 'throw';
        }, false);

</script>
@endpush