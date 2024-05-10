@extends('layouts.admin')


@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <a href="{{route('orders.create')}}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        <span>Erfassen</span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-12">
                    {!! Html::link('files/Vorlage.xlsx', 'Vorlage herunterladen', ['class' => 'font-medium text-blue-600 dark:text-blue-500 hover:underline']) !!}
                </div>
                <div class="col-lg-4 col-md-12">
                    <button id="chooseRoute" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Route hinzufügen</button>
                </div>
            </div>

            <hr>
        </header>
        <div id="filter_btns">
            <div id="pickup_btn">
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800 active">Alle</button>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Abholen</button>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Keine Route</button>
                    </div>
                </div>
            </div>
            <div id="status_btn">
                <br>
                <div class="row">
                    <div class="col-lg-2 col-md-12">
                        <button class="btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900 active">Alle</button>
                    </div>
                    @foreach ($order_statuses as $order_status)
                    <div class="col-lg-2 col-md-12">
                        <button class="btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">{{$order_status}}</button>
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
                <table class="table-striped table-bordered table responsive" id="datatable">
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
                    {!! Form::file('csv_file', ['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400']) !!}
                </div>
                {{ Form::submit('Bestellungen hochladen', ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800']) }}
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
                    {{-- @if ($routes->isNotEmpty()) --}}
                    <div class="form-group">
                        <label for="routes_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Route</label>
                        <select class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" name="routes_id">

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
                    {{-- @endif --}}
                    <div class="form-group">
                        <label for="Name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" name="Name" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    </div>
                    <div class="form-group">
                        <button data-remote='{{route('orders.createRoute')}}' id="createRoute"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Route zuweisen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="module">
    // document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                var table = $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    pageLength: 50,
                    buttons: [],
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
        // }, false);

</script>
@endpush