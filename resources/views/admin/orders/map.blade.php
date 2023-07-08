@extends('layouts.admin')


@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->

            <div class="row">
                <div class="col-sm-3">
                    <table class="table table-borderless" id="btns">
                        <tbody>
                        <td>
                            <table class="table table-borderless" id="city_btn">
                                <tbody>
                                <tr>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm active">Alle</button>
                                    </td>
                                </tr>
                                @foreach ($cities as $index => $city)
                                    <tr>
                                        <td>
                                            <button value="{{$index}}" class=" btn btn-outline-primary btn-sm
                                            ">{{$city}}</button>
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
                                        <button class="btn btn-outline-secondary btn-sm active">Alle</button>
                                    </td>
                                </tr>
                                @foreach ($statuses as $status)
                                    <tr>
                                        <td>
                                            <button class="btn btn-outline-secondary btn-sm">{{$status}}</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-9">
                    <div style="height: 800px" id="map-canvas"></div>
                </div>
            </div>

        </div>
    </section>

@endsection
@section('scripts')
    @include('includes.google-maps')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            // Get the container element
            var btnContainer_city = document.getElementById("city_btn");

            // Get all buttons with class="btn" inside the container
            var btns_city = btnContainer_city.getElementsByClassName("btn");

            // Loop through the buttons and add the active class to the current/clicked button
            for (var i = 0; i < btns_city.length; i++) {
                btns_city[i].addEventListener("click", function () {
                    var current = btnContainer_city.getElementsByClassName("active");
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
            for (i = 0; i < btns.length; i++) {
                btns[i].addEventListener("click", function () {
                    var active_btn = btnContainer.getElementsByClassName("active");
                    var city_btn = active_btn[0];
                    console.log(city_btn.value);
                    var status_btn = active_btn[1];
                    $.ajax({
                        url: "{!! route('orders.mapfilter')!!}",
                        data: {
                            city: city_btn.value,
                            status: status_btn.textContent
                        },
                        success: function (response) {
                            setMapsArguments(response, @json($key))
                            initialize();
                        }
                    });
                });
            }

            setMapsArguments(@json($orders), @json($key), @json($center));
            window.onload = loadScript;
        });
    </script>
@endsection
