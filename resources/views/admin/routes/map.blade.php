@extends('layouts.admin')


@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Karte</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <div class="row">  
                <div class="col-sm-3">
        
                <!-- Page Header-->
                <header> 
                    <h1 class="h3 display">Karte der Routen</h1>
                </header>
                        <table class="table table-borderless" id="btns">
                            <tbody>
                                <td>
                                    <table class="table table-borderless" id="route_btn">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <button class="btn btn-outline-primary btn-sm active">Alle</button>    
                                                </td>
                                            </tr>
                                            @foreach ($routes as $route)
                                            <tr>
                                                <td>
                                                    <button class="btn btn-outline-primary btn-sm">{{$route}}</button>
                                                </td>
                                            </tr>
                                            @endforeach 
                                        </tbody>
                                    </table>
                                    
                                </td>
                                <td>
                                    <table class="table table-borderless"  id="status_btn">
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
    <script>

    // Get the container element
    var btnContainer_route = document.getElementById("route_btn");

    // Get all buttons with class="btn" inside the container
    var btns_route = btnContainer_route.getElementsByClassName("btn");

    // Loop through the buttons and add the active class to the current/clicked button
    for (var i = 0; i < btns_route.length; i++) {
        btns_route[i].addEventListener("click", function() {
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
        btns_status[i].addEventListener("click", function() {
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
        btns[i].addEventListener("click", function() {
            active_btn = btnContainer.getElementsByClassName("active");
            route_btn = active_btn[0];
            status_btn = active_btn[1];
            $.ajax({
                url: "{!! route('routes.mapfilter')!!}",
                data: {
                    route: route_btn.textContent,
                    status: status_btn.textContent
                },
                success:function(response){
                    initialize(response);
                }
            });
        });
    }




    function loadScript() {
        navigator.geolocation.getCurrentPosition(success, error);
    }

    function success(pos) {
        // var crd = pos.coords;
        // latitude = crd.latitude;
        // longitude = crd.longitude;
    

        var script = document.createElement('script');

        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw&' +
        'callback=initialize';
        document.body.appendChild(script);
    };
    function error(err) {
        var center = @json($center);
        var latitude = center['lat'];
        var longitude =  center['lng'];
        var script = document.createElement('script');

        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw&' +
        'callback=initialize';
        document.body.appendChild(script);
    };

    window.onload = loadScript;

    function initialize(orders) {
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        // var locations = [
        //     @foreach($orders as $order)
        //     [$order->address['lat'],{{$order->address['lng']}}]
        //     @endforeach
        //     ];
        var center = @json($center);
        var LatLngList = new Array (new google.maps.LatLng(center['lat'],center['lng']));
        if(orders==undefined){
            var orders = @json($orders);
        }
        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var gData = new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']);
            LatLngList.push(gData);
        }

        
        var bounds = new google.maps.LatLngBounds ();
        map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
        var markers = [];
        var infowindow = new google.maps.InfoWindow();		
        function bindInfoWindow(marker, map, html) {
            google.maps.event.addListener(marker, 'mouseover', function() {
                infowindow.setContent(html);
                infowindow.open(map, marker);
            });
            google.maps.event.addListener(marker, 'mouseout', function() {
                infowindow.close();
            });	 
        }


        var html = "<p><b>"+center['name']+"</b> <br/>"+center['street'];
        var marker = new mapIcons.Marker({
            position: new google.maps.LatLng(center['lat'], center['lng']),
            map: map,
            html : html,
            icon: {
                path: mapIcons.shapes.MAP_PIN,
                fillColor: '#00CCBB',
                fillOpacity: 1,
                strokeColor: '',
                strokeWeight: 0
            },
	        map_icon_label: '<span class="map-icon map-icon-local-government"></span>'
            
        });
        markers.push(marker), bindInfoWindow(marker, map, html);


        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var icon_url = (order) => order.route_id==null ? "http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png" : "http://maps.gstatic.com/mapfiles/markers2/marker.png";
            var html = "<p><b>"+orders[i].address['firstname']+" "+orders[i].address['name']+"</b> <br/>"+orders[i].address['street']+"<br/> Zopf: "+orders[i]['quantity'];
            var marker = new mapIcons.Marker({
                position: new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']),
                map: map,
                html : html,
                icon: new google.maps.MarkerImage(icon_url(orders[i]))              
            });
            markers.push(marker), bindInfoWindow(marker, map, html);
        }

        var markerClusteres = null;
        markerClusteres = new MarkerClusterer(map, markers,{
            imagePath: 'https://cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m', zoomOnClick: false
        });	
        google.maps.event.addListener(markerClusteres, 'clusterclick', function(cluster){
            var content ='';
            var info = new google.maps.MVCObject;
            info.set('position', cluster.center_);
            var clickedMarkers = cluster.getMarkers();
            for (var i = 0; i < clickedMarkers.length; i++) {
                var html = clickedMarkers[i].html;
                content +=html;
            }
            infowindow.setContent(content);
            infowindow.open(map,info);
        });
    
        for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
            bounds.extend (LatLngList[i]);
        }

        map.fitBounds(bounds);
    }
    
    </script>
@endsection