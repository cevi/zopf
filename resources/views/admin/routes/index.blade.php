@extends('layouts.admin')


@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Routen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Routen</h1>
                <a href="{{route('routes.create')}}" class="btn btn-primary btn-success btn-sm">
                    <span>Erstellen</span>
                </a>
            </header>
            <div class="row">            
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th scope="col" width="20%">Name</th>
                                <th scope="col" width="15%">Verantwortlich</th>
                                <th scope="col" width="10%">Routen Art</th>
                                <th scope="col" width="10%">Anz. Zöpfe</th>
                                <th scope="col" width="10%">Anz. Bestellungen</th>
                                <th scope="col" width="10%">Status</th>
                                <th scope="col" width="20%">Aktionen</th>
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
            ajax: "{!! route('routes.CreateDataTables') !!}",
            columns: [
                   { data: 'name', name: 'name' },
                   { data: 'user', name: 'user' },
                   { data: 'routetype', name: 'routetype' },
                   { data: 'zopf_count', name: 'zopf_count' },
                   { data: 'order_count', name: 'order_count' },
                   { data: 'status', name: 'status'},
                   { data: 'Actions', name: 'Actions', orderable:false,serachable:false,sClass:'text-center'},
                   
                ]
       });
    });
    
    
    </script>
@endsection