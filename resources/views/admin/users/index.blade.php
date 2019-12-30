@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Leiter</li>
            </ul>
        </div>
    </div>
    @if (Session::has('deleted_user'))
        <p class="bg-danger">{{session('deleted_user')}}</p> 
    @endif
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Leiter</h1>
                <a href="{{route('users.create')}}" type="button" class="btn btn-success btn-sm ">Erstellen</a>
            </header>
            <div class="row">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th>Name</th>
                            <th>Gruppe</th>
                            <th>Rolle</th>
                            <th>Aktiv</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                 </table>
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
            ajax: "{!! route('users.CreateDataTables') !!}",
            columns: [
                //    {data: 'checkbox', name: 'checkbox', orderable:false,serachable:false,sClass:'text-center'},
                   { data: 'username', name: 'username' },
                   { data: 'group', name: 'group' },
                   { data: 'role', name: 'role' },
                   { data: 'active', name: 'active' },
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