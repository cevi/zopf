@extends('home.layout')

@section('route_content')
    <h1>{{$route->name}}</h1> <br>

    <div>
    <a type="button" class="btn active btn-info btn" href="{{route('home.routes',$route->id)}}">Liste</a>
    <a type="button" class="btn btn-info btn" href="#">Karte</a>
    </div> <br>                     
    <!-- Author -->
    @if ($orders)
                                
        <div class="col-sm-9">
            <div style="height: 600px" id="map-canvas"></div>
        </div>
    @endif
        
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
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var center = @json($center);
        var LatLngList = new Array (new google.maps.LatLng(center['lat'],center['lng']));
        var orders = @json($orders);
        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var gData = new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']);
            LatLngList.push(gData);
        }

        
        var bounds = new google.maps.LatLngBounds ();
        infoWindowClosed = true;
        map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
        var markers = [];
        var infowindow = new google.maps.InfoWindow();		
        function bindInfoWindow(marker, map, html) {
            google.maps.event.addListener(marker, 'click', function() {
                if (infoWindowClosed) {
                    infowindow.setContent(html);
                    infowindow.open(map, marker);
                    infoWindowClosed = false;
                } else {
                    infowindow.close(map, marker);
                    infoWindowClosed = true;
                }
            });
        	 
        }


        var html = "<p><b>"+center['name']+"</b> <br/>"+center['street'];
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(center['lat'], center['lng']),
            map: map,
            html : html          
        });
        markers.push(marker), bindInfoWindow(marker, map, html);


        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var icon_url = (order) => order.route_id==null ? "http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png" : "http://maps.gstatic.com/mapfiles/markers2/marker.png";
            var html = "<p><b>"+orders[i].address['firstname']+" "+orders[i].address['name']+"</b> <br/>"+orders[i].address['street']+"<br/> Zopf: "+orders[i]['quantity'];
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']),
                map: map,
                html : html             
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