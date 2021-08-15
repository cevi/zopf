var center, orders, latitude, longitude;

function setMapsArguments(orders, key, center = null, travelMode = null, cluster = true){
	this.center = center;
	this.orders = orders;
	this.key = key;
	this.travelMode = travelMode;
	this.cluster = cluster;
}

function loadScript() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(success, error);
	} else {
		error();
	}
}
	
function success(pos) {
	var crd = pos.coords;
	latitude = crd.latitude;
	longitude = crd.longitude;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?key='+ key +'&callback=initialize';
	document.body.appendChild(script);
}
	
function error(err) {
	latitude = center.lat;
	longitude = center.lng;
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?key='+ key +'&callback=initialize';
	document.body.appendChild(script);
}


function initialize() {
	var directionsService = new google.maps.DirectionsService;
    var directionsRenderer = new google.maps.DirectionsRenderer(
        {
        suppressMarkers: true

        }
	);
	
	var mapOptions = {
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	var LatLngList = new Array;

	if(center != null){
		var gData = new google.maps.LatLng(center['lat'], center['lng']);
		LatLngList.push(gData);
		if(latitude != center['lat']){
			var gData = new google.maps.LatLng(latitude, longitude);
		}
		LatLngList.push(gData);
	}

	for (var i = 0, order_len = orders.length; i < order_len; i++) {
		if(orders[i].order_status_id <= 15){
			var gData = new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']);
			LatLngList.push(gData);
		}
	}
	
	var bounds = new google.maps.LatLngBounds ();
	map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

	if(travelMode != null){
		directionsRenderer.setMap(map);
		var waypts = [];
		for (var i = 1; i < LatLngList.length; i++) {
			waypts.push({
				location: LatLngList[i],
				stopover: true
			});
		}
		directionsService.route({
			origin: LatLngList[1],
			destination: LatLngList[0],
			waypoints: waypts,
			optimizeWaypoints: true,
			travelMode: travelMode
		}, function(response, status) {
			if (status === 'OK') {
				directionsRenderer.setDirections(response);
				// console.log(response);
			} else {
				window.alert('Directions request failed due to ' + status);
			}
		});
	};
	
	var markers = [];
	var infowindow = new google.maps.InfoWindow();	
	var infoWindowClosed = true;			
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

	if(center != null){
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
	

		if(latitude != center['lat']){
			var html = "<p><b>Deine Position</b>";
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(latitude, longitude),
				map: map,
				html : html          
			});
			markers.push(marker), bindInfoWindow(marker, map, html);
		}
	}

	var icon_url = (order) => ((travelMode != null) && (orders[i].order_status_id > 15)) || ((travelMode == null) && (order.route_id==null)) ? "http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png" : "http://maps.gstatic.com/mapfiles/markers2/marker.png";
            
	for (var i = 0, order_len = orders.length; i < order_len; i++) {
		var html = "<p><b>"+orders[i].address['firstname']+" "+orders[i].address['name']+"</b> <br/>"+orders[i].address['street']+"<br/> Zopf: "+orders[i]['quantity'];
		var marker = new mapIcons.Marker({
			position: new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']),
			map: map,
			html : html,
			icon: new google.maps.MarkerImage(icon_url(orders[i]))              
		});
		markers.push(marker), bindInfoWindow(marker, map, html);
	}

	if(cluster == true){
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
	}

	for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
		bounds.extend (LatLngList[i]);
	}

	map.fitBounds(bounds);
}