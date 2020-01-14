$db = new PDO('mysql:host=' . $MYSQL_server . ';dbname=' .$MYSQL_database . ';charset=utf8', $MYSQL_user, $MYSQL_password);
					function loadScript() {
						navigator.geolocation.getCurrentPosition(success, error);
					}

					function success(pos) {
						var crd = pos.coords;
						latitude = crd.latitude;
						longitude = crd.longitude;

						var script = document.createElement('script');

						script.type = 'text/javascript';
						script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw&' +
						'callback=initialize';
						document.body.appendChild(script);
					};
					function error(err) {
						var latitude = 47.4229133;
						var longitude = 8.4101146;
						var script = document.createElement('script');

						script.type = 'text/javascript';
						script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw&' +
						'callback=initialize';
						document.body.appendChild(script);
					};

					window.onload = loadScript;

					function initialize() {
						var mapOptions = {
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};

	// 					var LatLngList = new Array (
	// <?php
	// 						unset($sql);

	// 						if (isset($_GET['ort'])) {
	// 							$sql[] = " ort = '".$_GET['ort']."' ";
	// 						}
							
	// 						if(isset($_GET['route']) AND $_GET['route'] == "Keine") {
	// 							$sql[] = " route = '' ";
	// 						}
	// 						elseif(isset($_GET['route'])) {
	// 							$sql[] = " route = '".$_GET['route']."' ";
	// 						}
							
	// 						if(isset($_GET['selection'])) {
	// 							$sql[] = " status = '".$_GET['selection']."' ";
	// 						}  
								
	// 						$query = "SELECT * From ".$mysqldb;

	// 						if (!empty($sql)) {
	// 							$query .= ' WHERE ' . implode(' AND ', $sql);
	// 						}
							
	// 						$res = $db->query($query);
	// 						$first=false;
	// 						while ($dsatz = $res->fetch(PDO::FETCH_ASSOC)) {
	// 							if($first==true) echo ", ";
	// 							$first = true;
	// 							echo "new google.maps.LatLng(".$dsatz['lat'].",".$dsatz['lng'].")";

	// 						}
	// 						echo ");\n";
	// ?>
	// 					var bounds = new google.maps.LatLngBounds ();
	// 					map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
	// 					var markers = [];
	// 					var infowindow = new google.maps.InfoWindow();		
	// 					function bindInfoWindow(marker, map, html) {
	// 						google.maps.event.addListener(marker, 'mouseover', function() {
	// 							infowindow.setContent(html);
	// 							infowindow.open(map, marker);
	// 						});
	// 						google.maps.event.addListener(marker, 'mouseout', function() {
	// 							infowindow.close();
	// 						});	 
	// 					}
	// <?php
	// 					$res = $db->query($query);
	// 					$i = 0;
	// 					while ($dsatz = $res->fetch(PDO::FETCH_ASSOC)) {
	// ?>
	// 						var html = "<p><b>" + "<?php echo $dsatz['vorname']. " ".$dsatz['nachname'];?>" + "</b> <br/>" + "<?php echo $dsatz['strasse'];?>" + "<br/>" + "<?php echo "Zopf: ".$dsatz['anzahl'];?>";
							
	// 						var marker = new google.maps.Marker({
	// 							position: new google.maps.LatLng(<?php echo $dsatz['lat']; ?>, <?php echo $dsatz['lng']; ?>),
	// 							map: map,
	// <?php
	// 							if($dsatz['route'] == ''){
	// ?>
	// 								icon: new google.maps.MarkerImage("http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png"),
	// <?php
	// 							}
	// 							if($_GET['action']=="print"){
	// ?>
	// 								icon: new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=<?php echo $dsatz['reihe'] + 1;?>|FE6256|000000"),
	// <?php
	// 							}				  
	// ?>
	// 							html : html
							  
	// 						});
	// 						markers.push(marker), bindInfoWindow(marker, map, html);
								
	// <?php
	// 					}
	// 					if(isset($_GET['route'])){
	// 						$res = $db->query($query);
	// 						$details_url = "https://maps.googleapis.com/maps/api/directions/json?origin=47.4229133,8.4101146&destination=47.4229133,8.4101146&waypoints=optimize:true";
	// 						while ($dsatz = $res->fetch(PDO::FETCH_ASSOC)) {
	// 							$details_url .= "|".$dsatz['lat'].",".$dsatz['lng'];
	// 						}
	// 						$details_url .= "&key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw";
	// 						$ch = curl_init();
	// 						curl_setopt($ch, CURLOPT_URL, $details_url);
	// 						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 						$response = json_decode(curl_exec($ch), true);
							
	// 						$order = $response['routes'][0]['waypoint_order'];
	// 						$res = $db->query($query);
	// 						$all = $res->fetchAll();
	// 						for ($i = 0; $i < count($order); $i++){
	// 							$id = $all[$order[$i]]['id'];	
	// 							$db->query("UPDATE ".$mysqldb." SET reihe = '".$i."' WHERE id = '".$id."'");
	// 						}
	// 						$res = $db->query($query);
	// 						$all = $res->fetchAll();
	// ?>
	// 						var flightPlanCoordinates = new Array(
	// <?php 
	// 							$first=false;
	// 							for ($i=0;$i<count($response['routes'][0]['legs']);$i++){
	// 								for ($j=0;$j<count($response['routes'][0]['legs'][$i]['steps']);$j++){
	// 									if($first==true) echo ", ";
	// 									$first = true;
	// 									echo "new google.maps.LatLng(".$response['routes'][0]['legs'][$i]['steps'][$j]['start_location']['lat'].",".$response['routes'][0]['legs'][$i]['steps'][$j]['start_location']['lng']."), ";			
	// 									echo "new google.maps.LatLng(".$response['routes'][0]['legs'][$i]['steps'][$j]['end_location']['lat'].",".$response['routes'][0]['legs'][$i]['steps'][$j]['end_location']['lng'].")";
	// 								}
	// 							}
	// ?>
	// 						);
	// 						var flightPath = new google.maps.Polyline({
	// 							path: flightPlanCoordinates,
	// 							geodesic: true,
	// 							strokeColor: '#FF0000',
	// 							strokeOpacity: 1.0,
	// 							strokeWeight: 2
	// 						});

	// 						flightPath.setMap(map);
	// 						for (var i = 0, flightpathLen = flightPlanCoordinates.length; i < flightpathLen; i++) {
	// 							LatLngList.push (flightPlanCoordinates[i]);
	// 						}
	// <?php
	// 					}
	// ?>
	// 						var markerClusteres = null;
	// 						markerClusteres = new MarkerClusterer(map, markers,{
	// 							imagePath: 'https://cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m', zoomOnClick: false
	// 						});	
	// 						google.maps.event.addListener(markerClusteres, 'clusterclick', function(cluster){
	// 							var content ='';
	// 							var info = new google.maps.MVCObject;
	// 							info.set('position', cluster.center_);
	// 							var clickedMarkers = cluster.getMarkers();
	// 							for (var i = 0; i < clickedMarkers.length; i++) {
	// 								var html = clickedMarkers[i].html;
	// 								content +=html;
	// 							}
	// 							infowindow.setContent(content);
	// 							infowindow.open(map,info);
	// 						});
	// 					google.maps.event.addListener(map, "click", function(){
	// 						infowindow.close();
	// 					});

	// 					for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
	// 						bounds.extend (LatLngList[i]);
	// 					}

	// 					map.fitBounds(bounds);
					}