@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
                <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <h1 class="h3 display">Route {{$route->name}}</h1>
                <a href="{{route('routes.overview', $route)}}" class="btn btn-info btn-sm">Routen Übersicht</a>
            </header>
            <div class="row">

                <div class="col-sm-6">
                    @if ($route->route_status_id == config('status.route_vorbereitet'))
                        <div class="alert alert-danger">Verantwortliche Person beachten.</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    {!! Form::model($route, ['method' => 'Patch', 'action'=>['AdminRoutesController@update',$route->id]]) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', 'Verantwortlicher:') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('route_type_id', 'Rolle:') !!}
                        {!! Form::select('route_type_id', $route_types, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('route_status_id', 'Status:') !!}
                        {!! Form::select('route_status_id', $route_statuses, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Route Aktualisieren', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}

                    {!! Form::model($route, ['method' => 'DELETE', 'action'=>['AdminRoutesController@destroy',$route->id]]) !!}
                    <div class="form-group">
                        {!! Form::submit('Route löschen', ['class' => 'btn btn-danger'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
                <div class="col-sm-6">
                    <button id="chooseOrders" class="btn btn-info btn-sm">Bestellungen hinzufügen</button>
                    @if ($orders)
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Vorname</th>
                                <th scope="col">Strasse</th>
                                <th scope="col">PLZ</th>
                                <th scope="col">Ort</th>
                                <th scope="col">Anzahl</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            @foreach ($orders as $order)
                                <tbody>
                                <tr>
                                    <td>{{$order->address['name']}}</td>
                                    <td>{{$order->address['firstname']}}</td>
                                    <td>{{$order->address['street']}}</td>
                                    <td>{{$order->address['plz']}}</td>
                                    <td>{{$order->address['city']}}</td>
                                    <td>{{$order['quantity']}}</td>
                                    <td>{{$order->order_status['name']}}</td>
                                    <td>
                                        {!! Form::model($order, ['method' => 'patch', 'action'=>['AdminRoutesController@RemoveOrder',$order->id]]) !!}
                                        {!! Form::hidden('order_id', $order->id) !!}
                                        {!! Form::hidden('route_id', $route->id) !!}
                                        {!! Form::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-sm rounded-0', 'title' => 'Aus Route entfernen', 'type' => 'submit'])!!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                </tbody>
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>
        </div>
        {{--        <div style="height: 800px" id="map-canvas"></div>--}}
    </section>
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Bestellungen Auswählen</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <button id="AssignOrders" class="btn btn-info btn-sm">Bestellungen zuweisen</button>
                    </div>
                    @if ($open_orders)
                        <div class="row">
                            <div class="col-md-6">
                                <form id="modal-form" method="POST" action="javascript:void(0)"
                                      style="height:1000px;overflow-y:scroll">
                                    <table class="table table-striped table-bordered" id="modal-datatable">
                                        <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Vorname</th>
                                            <th scope="col">Strasse</th>
                                            <th scope="col">PLZ</th>
                                            <th scope="col">Ort</th>
                                            <th scope="col">Anzahl</th>
                                            <th scope="col" style="width:15%">Kommentar</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </form>
                            </div>
                            <div class="col-md-6">

                                <div class="row">
                                    <div class="col-md-6">
                                        <button id="ResizeMap" class="btn btn-info btn-sm">Karte zentrieren</button>
                                    </div>
                                </div>
                                <br>
                                <div style="height: 800px" id="map-canvas"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @include('includes.google-maps')
    <script>
        setMapsArguments(@json($open_orders), @json($key), @json($center), null, false, true);
        loadScript();
        $('#chooseOrders').on('click', function () {
            $('#ajaxModel').modal('show');
        });
        $('#ResizeMap').on('click', function () {
            MapResize();
        });

        function CheckboxClick(checkbox) {
            MapResize();
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id == parseInt(checkbox.value)) {
                    var marker = markers[i];
                }
            }
            ChangeClick(marker);
        }

        function MarkerClick(marker) {
            ChangeClick(marker)
            $('#modal-datatable input[type=checkbox]').each(function () {
                if (parseInt(this.value) === marker.id) {
                    this.checked = !this.checked;
                }
            });
        }

        function FillIDList(id) {
            if (clicked_markers.includes(id)) {
                for (var i = 0; i < clicked_markers.length; i++) {
                    if (clicked_markers[i] === id) {
                        clicked_markers.splice(i, 1);
                        result = false;
                    }
                }
            } else {
                clicked_markers.push(id)
            }
            return clicked_markers.includes(id);
        }

        function ChangeClick(marker) {
            if (FillIDList(marker.id)) {
                marker.setIcon("https://maps.gstatic.com/mapfiles/markers2/marker.png");
            } else {
                marker.setIcon("https://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png");
            }
        }


        $('#AssignOrders').on('click', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            // confirm then
            $.ajax({
                url: '{{route('routes.AssignOrders')}}',
                method: 'POST',
                data: {
                    id: clicked_markers,
                    route_id: @json($route['id'])},
                success: function (data) {
                    $('#ajaxModel').modal('hide');
                    location.reload();
                }
            });
        });
        $(document).ready(function () {
            $('#modal-datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                language: {
                    "url": "/lang/Datatables.json"
                },
                ajax: "{!! route('routes.CreateModalDataTables') !!}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'firstname', name: 'firstname'},
                    {data: 'street', name: 'street'},
                    {data: 'plz', name: 'plz'},
                    {data: 'city', name: 'city'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'comments', name: 'comments'},
                    {data: 'checkbox', name: 'checkbox', orderable: false, serachable: false, sClass: 'text-center'},

                ],
                "order": [[3, 'asc'], [2, 'asc'], [0, 'asc']]
            });
        });
    </script>
@endsection
