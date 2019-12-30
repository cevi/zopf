@extends('layouts.admin')


@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Bestellungen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Bestellungen</h1>
                <a href="{{route('orders.create')}}" class="btn btn-primary btn-success btn-sm">
                    <span>Erstellen</span>
                </a>
                {{-- <button data-remote='{{route('orders.createRoute')}}' id="createRoute" class="btn btn-info btn-sm">Route hinzufügen</button> --}}
                <button id="chooseRoute" class="btn btn-info btn-sm">Route hinzufügen</button>


            </header>
            <div class="row">            
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Vorname</th>
                                <th scope="col">Strasse</th>
                                <th scope="col">PLZ</th>
                                <th scope="col">Ort</th>
                                <th scope="col">Anzahl</th>
                                <th scope="col">Route</th>
                                <th scope="col">Status</th>
                                <th scope="col">Auswahl</th>
                                <th scope="col">Aktionen</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>

    </section>
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Route Auswählen</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form" method="POST" action="javascript:void(0)">
                        {{-- @if ($routes->isNotEmpty())                       --}}
                            <div class="form-group">
                                <label for="routes_id">Route</label>
                                <select class="form-control" name="routes_id">
   
                                    <option>Wähle Route</option>
                                    @foreach ($routes as $route)
                                      <option value="{{ $route->id }}"> 
                                          {{ $route->name }} 
                                      </option>
                                    @endforeach    
                                  </select>
                            </div>
                        {{-- @endif  --}}
                        <div class="form-group">
                            <label for="Name">Name</label>
                            <input type="text" name="Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <button data-remote='{{route('orders.createRoute')}}' id="createRoute" class="btn btn-info btn-sm">Route zuweisen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
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
            ajax: "{!! route('orders.CreateDataTables') !!}",
            columns: [
                   { data: 'name', name: 'name' },
                   { data: 'firstname', name: 'firstname' },
                   { data: 'street', name: 'street' },
                   { data: 'plz', name: 'plz' },
                   { data: 'city', name: 'city' },
                   { data: 'quantity', name: 'quantity' },
                   { data: 'route', name: 'route' },
                   { data: 'status', name: 'status' },
                   { data: 'checkbox', name: 'checkbox', orderable:false,serachable:false,sClass:'text-center'},
                   { data: 'Actions', name: 'Actions', orderable:false,serachable:false,sClass:'text-center'},
                   
                ]
       });
    });
    $('#datatable').on('click', '.btn-danger[data-remote]', function (e) { 
        e.preventDefault();
        // if(confirm("Are you sure you want to Delete this data?"))
        // {
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
        // }
        // else
        // {
        //     return false;
        // }
    });

    $('#chooseRoute').on('click', function () { 
        
        var id = [];
        $('#datatable input[type=checkbox]:checked').each(function(){
            id.push($(this).val());
        });
        if(id.length > 0)
        {
            $('#ajaxModel').modal('show');
        }
        else
        {
            alert("Eine Bestellung auswählen");    
        }
    });

    $('#createRoute').on('click', function () { 
        
        var id = [];
        $('#datatable input[type=checkbox]:checked').each(function(){
            id.push($(this).val());
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        var url = $(this).data('remote');
        // confirm then
        $.ajax({
            url: url,
            method: 'POST',
            data:{id:id,
               name: $('#modal-form input[name="Name"]').val(),
               route_id: $('#modal-form select[name="routes_id"]').val()},
            success:function(data)
            {   
                $('#ajaxModel').modal('hide');
                $('#datatable').DataTable().ajax.reload();
            }
        });
    });

    
    </script>
@endsection