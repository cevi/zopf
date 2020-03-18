@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/routes">Routen</a></li>
            <li class="breadcrumb-item active">Übersicht</li>
            </ul>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
            <h1 class="h3 display">Übersicht {{$route['name']}}</h1>
                Total Anzahl Zöpfe: {{$orders->sum('quantity')}}    <br>          
                Routen Art: {{$routetype['name']}}                  <br>
                @if ($route->route_status['id']>5)
                    <a type="button" class="btn btn-info btn-sm" href="{{route('routes.downloadPDF', $route->id)}}">Download PDF</a>
                @endif
            </header>
            <div class="row">
                <div class="col-sm-6">
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
                            </tr>
                        </tbody>
                    @endforeach
                    </table>
                
                @endif
                </div>
                <div class="col-sm-6">
                    <div style="height:630px" id="map-canvas"></div>
                </div>
                    
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>

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
    var directionsService = new google.maps.DirectionsService;
    var directionsRenderer = new google.maps.DirectionsRenderer(
        {
        suppressMarkers: true

        }
    )
    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
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
    
    directionsRenderer.setMap(map);
    var waypts = [];
    for (var i = 1; i < LatLngList.length; i++) {
        waypts.push({
            location: LatLngList[i],
            stopover: true
        });
    }
    directionsService.route({
        origin: LatLngList[0],
        destination: LatLngList[0],
        waypoints: waypts,
        optimizeWaypoints: true,
        travelMode: '{{$routetype['travelmode']}}'
    }, function(response, status) {
        if (status === 'OK') {
        directionsRenderer.setDirections(response);
        var route = response.routes[0];
        } else {
        window.alert('Directions request failed due to ' + status);
        }
    });

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

    for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
        bounds.extend (LatLngList[i]);
    }

    map.fitBounds(bounds);
}

</script>
    
    @endsection
    