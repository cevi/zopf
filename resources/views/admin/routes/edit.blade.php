@extends('layouts.admin')

@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            <a href="{{route('routes.overview', $route)}}" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-center text-sm px-3 py-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Routen Übersicht</a>
        </header>
        <div class="row">

            <div class="col-sm-6">
                @if ($route->route_status_id == config('status.route_vorbereitet'))
                <div class="alert alert-danger">Verantwortliche Person beachten.</div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                {!! Form::model($route, ['method' => 'Patch', 'action'=>['AdminRoutesController@update',$route->id]])
                !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::text('name', null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('user_id', 'Verantwortlicher:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('user_id', $users, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('route_type_id', 'Routen Art:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('route_type_id', $route_types, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('route_status_id', 'Status:', ['class' =>'block mb-2 text-sm font-medium text-gray-900 dark:text-white']) !!}
                    {!! Form::select('route_status_id', $route_statuses, null, ['class' => 'mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Route Aktualisieren', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'])!!}
                </div>
                {!! Form::close()!!}

                {!! Form::model($route, ['method' => 'DELETE', 'action'=>['AdminRoutesController@destroy',$route->id]])
                !!}
                <div class="form-group">
                    {!! Form::submit('Route löschen', ['class' => 'focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'])!!}
                </div>
                {!! Form::close()!!}
            </div>
            <div class="col-sm-6">
                <button id="chooseOrders" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-center text-sm px-3 py-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Bestellungen hinzufügen</button>
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
                            <td><a href="{{route('orders.edit',$order)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$order->address['name']}}</a></td>
                            <td>{{$order->address['firstname']}}</td>
                            <td>{{$order->address['street']}}</td>
                            <td>{{$order->address['plz']}}</td>
                            <td>{{$order->address['city']}}</td>
                            <td>{{$order['quantity']}}</td>
                            <td>{{$order->order_status['name']}}</td>
                            <td>
                                {!! Form::model($order, ['method' => 'patch',
                                'action'=>['AdminRoutesController@RemoveOrder',$order->id]]) !!}
                                {!! Form::hidden('order_id', $order->id) !!}
                                {!! Form::hidden('route_id', $route->id) !!}
                                {!! Form::button('<i class="fa fa-trash"></i>', ['class' => 'focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg  text-center text-sm px-3 py-2 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900', 'title' => 'Aus Route entfernen', 'type' => 'submit'])!!}
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
</section>
<div class="modal fade" id="ajaxModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">Bestellungen Auswählen</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <button id="AssignOrders" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Bestellungen zuweisen</button>
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
                                <button id="ResizeMap" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Karte zentrieren</button>
                            </div>
                        </div>
                        <br>
                        <div style="height: 800px" id="map-canvas" class="text-gray-900"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
    @include('includes.google-maps')
    <script type="module">
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

            document.addEventListener('DOMContentLoaded', function () {
                var open_order = @json($open_orders);
                var key = @json($key);
                var center = @json($center);
                setMapsArguments(open_order, key, center, null, false, true);
                loadScript();
                $('#chooseOrders').on('click', function () {
                    const myModal = new bootstrap.Modal('#ajaxModal');
                    myModal.show();
                });
                $('#ResizeMap').on('click', function () {
                    MapResize();
                });


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
                            const truck_modal = document.querySelector('#ajaxModal');
                            const modal = bootstrap.Modal.getInstance(truck_modal);
                            modal.hide();
                            location.reload();
                        }
                    });
                });
                $(document).ready(function () {
                    $('#modal-datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        pageLength: 25,
                        buttons: [],
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
                            {
                                data: 'checkbox',
                                name: 'checkbox',
                                orderable: false,
                                serachable: false,
                                sClass: 'text-center'
                            },

                        ],
                        "order": [[3, 'asc'], [2, 'asc'], [0, 'asc']]
                    });
                });
            }, false);
    </script>
@endpush