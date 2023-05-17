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
                <div class="row" style="width: 30%">
                    <div class="col-md-4">
                        <a href="{{route('orders.create')}}" class="btn btn-primary btn-sm">
                            <span>Erfassen</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        {!! Html::link('files/vorlage.xlsx', 'Vorlage herunterladen') !!}
                    </div>
                    <div class="col-md-4">
                        <button id="chooseRoute" class="btn btn-info btn-sm">Route hinzufügen</button>
                    </div>
                </div>

                <hr>
            </header>
            <div id="filter_btns">
                <div id="pickup_btn">
                    <div class="row" style="width: 30%">
                        <div class="col-md-4">
                            <button class="btn btn-primary active">Alle</button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary">Abholen</button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary">Keine Route</button>
                        </div>
                    </div>
                </div>
                <div id="status_btn">
                    <br>
                    <div class="row">
                        <div class="col-md-1">
                            <button class="btn btn-secondary active">Alle</button>
                        </div>
                        @foreach ($order_statuses as $order_status)
                            <div class="col-md-1">
                                <button class="btn btn-secondary">{{$order_status}}</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" value="Alle" id="pickup_btn_value">
            <input type="hidden" value="Alle" id="status_btn_value">
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table responsive" width="100%" id="datatable">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">Strasse</th>
                            <th scope="col">PLZ</th>
                            <th scope="col">Ort</th>
                            <th scope="col">Anzahl</th>
                            <th scope="col">Route</th>
                            <th scope="col">Abholung</th>
                            <th scope="col">Bemerkung</th>
                            <th scope="col">Status</th>
                            <th scope="col">Auswahl</th>
                            <th scope="col">Aktionen</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-4">
                    {!! Form::open(['action' => 'AdminOrdersController@uploadFile', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                    <div class="form-group">
                        {{ Form::file('csv_file',['class' => 'dropify'])}}
                    </div>
                    {{ Form::submit('Bestellungen hochladen', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>

        </div>

    </section>
    <div class="modal fade" id="ajaxModal" aria-hidden="true">
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
                                @if($routes)
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">
                                            {{ $route->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        {{-- @endif  --}}
                        <div class="form-group">
                            <label for="Name">Name</label>
                            <input type="text" name="Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <button data-remote='{{route('orders.createRoute')}}' id="createRoute"
                                    class="btn btn-info btn-sm">Route zuweisen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                var table = $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    pageLength: 50,
                    language: {
                        "url": "/lang/Datatables.json"
                    },
                    ajax: {
                        url: "{!! route('orders.CreateDataTables') !!}",
                        data: function (d) {
                            d.pickup = $('#pickup_btn_value').val()
                            d.status = $('#status_btn_value').val()
                        }
                    },
                    // order: [[9, 'asc']],
                    order: [[7, 'asc'], [6, 'asc'], [9, 'asc'], [3, 'asc'], [2, 'asc'], [0, 'asc']],
                    columns: [
                        {data: 'name', name: 'name', "width": "10%"},
                        {data: 'firstname', name: 'firstname', "width": "7%"},
                        {data: 'street', name: 'street', "width": "8%"},
                        {data: 'plz', name: 'plz', "width": "2%"},
                        {data: 'city', name: 'city', "width": "8%"},
                        {data: 'quantity', name: 'quantity', "width": "3%"},
                        {data: 'route', name: 'route', "width": "5%"},
                        {data: 'pick_up', name: 'pick_up', "width": "4%"},
                        {data: 'comments', name: 'comments', "width": "10%"},
                        // {data: 'status', name: 'status', "width": "5%"},
                        {
                            data: {
                                _: 'status.display',
                                sort: 'status.sort'
                            },
                            name: 'status',
                            "width": "5%"
                        },
                        {
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            serachable: false,
                            sClass: 'text-center',
                            "width": "5%"
                        },
                        {
                            data: 'Actions',
                            name: 'Actions',
                            orderable: false,
                            serachable: false,
                            sClass: 'text-center',
                            "width": "5%"
                        },

                    ]
                });
                // Get the container element
                var btnContainer_pickup = document.getElementById("pickup_btn");
                var btnContainer_status = document.getElementById("status_btn");

                // Get all buttons with class="btn" inside the container
                var btns_pickup = btnContainer_pickup.getElementsByClassName("btn");
                var btns_status = btnContainer_status.getElementsByClassName("btn");

                // Loop through the buttons and add the active class to the current/clicked button
                for (var i = 0; i < btns_pickup.length; i++) {
                    btns_pickup[i].addEventListener("click", function () {
                        var current = btnContainer_pickup.getElementsByClassName("active");
                        // If there's no active class
                        if (current.length > 0) {
                            current[0].className = current[0].className.replace(" active", "");
                        }

                        // Add the active class to the current/clicked button
                        this.className += " active";
                        var active_btn = this.textContent;
                        $('#pickup_btn_value').val(active_btn);
                    });
                }

                // Loop through the buttons and add the active class to the current/clicked button
                for (var i = 0; i < btns_status.length; i++) {
                    btns_status[i].addEventListener("click", function () {
                        var current = btnContainer_status.getElementsByClassName("active");
                        // If there's no active class
                        if (current.length > 0) {
                            current[0].className = current[0].className.replace(" active", "");
                        }

                        // Add the active class to the current/clicked button
                        this.className += " active";
                        var active_btn = this.textContent;
                        $('#status_btn_value').val(active_btn);
                    });
                }

                var btnsContainer = document.getElementById("filter_btns");

                // Get all buttons with class="btn" inside the container
                var btns = btnsContainer.getElementsByClassName("btn");

                // Loop through the buttons and add the active class to the current/clicked button
                for (var i = 0; i < btns.length; i++) {
                    btns[i].addEventListener("click", function () {
                        table.draw();
                    });
                }


            });
            $('#datatable').on('click', '.delete[data-remote]', function (e) {
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
                    // dataType: 'json',
                    // data: {method: 'DELETE', submit: true}
                }).always(function () {
                    $('#datatable').DataTable().draw(false);
                });
            });
            $('#datatable').on('click', '.pick-up[data-remote]', function (e) {
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
                    type: 'POST',
                    // dataType: 'json',
                    // data: {method: 'POST', submit: true}
                }).always(function () {
                    $('#datatable').DataTable().draw(false);
                });
            });

            $('#chooseRoute').on('click', function () {

                var id = [];
                $('#datatable input[type=checkbox]:checked').each(function () {
                    id.push($(this).val());
                });
                if (id.length > 0) {
                    const myModal = new bootstrap.Modal('#ajaxModal');
                    myModal.show();
                } else {
                    alert("Eine Bestellung auswählen");
                }
            });

            $('#createRoute').on('click', function () {

                var id = [];
                $('#datatable input[type=checkbox]:checked').each(function () {
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
                    data: {
                        id: id,
                        name: $('#modal-form input[name="Name"]').val(),
                        route_id: $('#modal-form select[name="routes_id"]').val()
                    },
                    success: function (data) {
                        $('#modal-form').trigger('reset');
                        const truck_modal = document.querySelector('#ajaxModal');
                        const modal = bootstrap.Modal.getInstance(truck_modal);
                        modal.hide();
                        $('#datatable').DataTable().ajax.reload();
                    }
                });
            });
        }, false);

    </script>
@endsection
