@extends('layouts.admin')


@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">

                <!-- Page Header-->
                <table class="table table-borderless" id="btns">
                    <tbody>
                        <td>
                            <table class="table table-borderless" id="route_btn">
                                <tbody>
                                    <tr>
                                        <td>
                                            <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800 active">Alle</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Keine</button>
                                        </td>
                                    </tr>
                                    @foreach ($routes as $route)
                                    <tr>
                                        <td>
                                            <button class="btn text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">{{$route}}</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </td>
                        <td>
                            <table class="table table-borderless" id="status_btn">
                                <tbody>
                                    <tr>
                                        <td>
                                            <button class="btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900 active">Alle</button>
                                        </td>
                                    </tr>
                                    @foreach ($statuses as $status)
                                    <tr>
                                        <td>
                                            <button class="btn text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">{{$status}}</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                </table>
            </div>
            <div class="col-sm-9">
                <div style="height: 800px" id="map-canvas" class="text-gray-900"></div>
            </div>
        </div>

    </div>
</section>

@endsection
@push('scripts')
@include('includes.google-maps')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                // Get the container element
                var btnContainer_route = document.getElementById("route_btn");

                // Get all buttons with class="btn" inside the container
                var btns_route = btnContainer_route.getElementsByClassName("btn");

                // Loop through the buttons and add the active class to the current/clicked button
                for (var i = 0; i < btns_route.length; i++) {
                    btns_route[i].addEventListener("click", function () {
                        var current = btnContainer_route.getElementsByClassName("active");
                        // If there's no active class
                        if (current.length > 0) {
                            current[0].className = current[0].className.replace(" active", "");
                        }

                        // Add the active class to the current/clicked button
                        this.className += " active";
                    });
                }

                // Get the container element
                var btnContainer_status = document.getElementById("status_btn");

                // Get all buttons with class="btn" inside the container
                var btns_status = btnContainer_status.getElementsByClassName("btn");

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
                    });
                }

                var btnContainer = document.getElementById("btns");

                // Get all buttons with class="btn" inside the container
                var btns = btnContainer.getElementsByClassName("btn");

                // Loop through the buttons and add the active class to the current/clicked button
                for (var i = 0; i < btns.length; i++) {
                    btns[i].addEventListener("click", function () {
                        var active_btn = btnContainer.getElementsByClassName("active");
                        var route_btn = active_btn[0];
                        var status_btn = active_btn[1];
                        $.ajax({
                            url: "{!! route('routes.mapfilter')!!}",
                            data: {
                                route: route_btn.textContent,
                                status: status_btn.textContent
                            },
                            success: function (response) {
                                setMapsArguments(response, @json($key));
                                initialize();
                            }
                        });
                    });
                }

                setMapsArguments(@json($orders), @json($key), @json($center))
                window.onload = loadScript;
            });
        }, false);
</script>
@endpush