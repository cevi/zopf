@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Aktionen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Aktionen</h1>
                <a href="{{route('actions.create')}}" type="button" class="btn btn-primary btn-sm ">Erfassen</a>
            </header>
            <div class="row">

                <div class="col-sm-9">
                    <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                        <thead>
                            <tr>
                                <th>Jahr</th>
                                <th>Name</th>
                                <th>Gruppe</th>
                                <th>Status</th>
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
    <script>
    $(document).ready(function(){
          $('#datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            language: {
                "url": "/lang/Datatables.json"
            },
            ajax: "{!! route('actions.CreateDataTables') !!}",
            columns: [
                   { data: 'year', name: 'year' },
                   { data: 'name', name: 'name' },
                   { data: 'group', name: 'group' },
                   { data: 'status', name: 'status' },
                   {data: 'Actions', name: 'Actions', orderable:false,serachable:false,sClass:'text-center'},
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
    </script>

@endsection
