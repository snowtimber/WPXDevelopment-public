<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=visualization"></script>
	<script src="/WPXDevelopment/Econ-Heatmap/arcgislink_compressed.js" type="text/javascript"></script>
    <script>
// Adding 500 Data Points
var map, pointarray, heatmap;

var taxiData = [

<?php
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//query bottomhole location and eur data from dim_well
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------

$con1=odbc_connect('DataVision','datavision_query','pw');
if (!$con1) {
  exit("Connection Failed: " . $con1);
}
//sample wellname:  $wellname = 'GM 341-14';
//$wellname = $_GET["well_name"];
//$sql="SELECT * FROM dbo.Dim_Well where well_name='".$wellname."'";
$sql="SELECT * FROM OneLine.pic_Wells";
$rs1=odbc_exec($con1,$sql);
if (!$rs1) {
  exit("Error in SQL");
}

$constant = 500000;
while (odbc_fetch_row($rs1)) {
  $dim_well_id=odbc_result($rs1,"dim_well_id");
  $pad=odbc_result($rs1,"pad");
  $field=odbc_result($rs1,"field");
  $well_name=odbc_result($rs1,"wellname");
  $api=odbc_result($rs1,"api");
  $eu=odbc_result($rs1,"EU Number");
  $spud_date=odbc_result($rs1,"Spud Date");
  $bottom_lat=odbc_result($rs1,"botlat");
  $bottom_lon=odbc_result($rs1,"botlon");
  $eur=odbc_result($rs1,"eur");
  $net_sand_completed=odbc_result($rs1,"Net Sand Completed");
  $gip2=odbc_result($rs1,"GIP2");
  $actual_total_cost=odbc_result($rs1,"Actual Total Cost");
  //$wsn=odbc_result($rs1,"wsn");
 // $ow_datum_elevation=odbc_result($rs1,"ow_datum_elevation");  
 
 if($eur==null){
 $eur = 0;
 }
  if($eur<0){
 $eur = 0;
 }

  if($actual_total_cost>0){
  echo " {location: new google.maps.LatLng(".$bottom_lat.", ".$bottom_lon."), weight: ".$eur * $constant / $actual_total_cost."},
  ";
	}
}
odbc_close($con1);
?>

//  {location: new google.maps.LatLng(37.782, -122.447), weight: 0.5},
  //new google.maps.LatLng(37.782, -122.445),
 // {location: new google.maps.LatLng(37.782, -122.443), weight: 2},
 // {location: new google.maps.LatLng(37.782, -122.441), weight: 3},
 // {location: new google.maps.LatLng(37.782, -122.439), weight: 2},
  //new google.maps.LatLng(37.782, -122.437),
 // {location: new google.maps.LatLng(37.782, -122.435), weight: 0.5},

//  {location: new google.maps.LatLng(37.785, -122.447), weight: 3},
 // {location: new google.maps.LatLng(37.785, -122.445), weight: 2},
  //new google.maps.LatLng(37.785, -122.443),
 // {location: new google.maps.LatLng(37.785, -122.441), weight: 0.5},
  //new google.maps.LatLng(37.785, -122.439),
 // {location: new google.maps.LatLng(37.785, -122.437), weight: 2},
 // {location: new google.maps.LatLng(37.785, -122.435), weight: 3}
];

function initialize() {
  var mapOptions = {
    zoom: 11,
    //center: new google.maps.LatLng(37.774546, -122.433523),
	center: new google.maps.LatLng(39.4597845, -108.0641645),
    mapTypeId: google.maps.MapTypeId.SATELLITE
  };
  
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  var pointArray = new google.maps.MVCArray(taxiData);
  
	// Load Chris Wold's ESRI ArcGIS Mapserver for well details
	//var ctaLayer = new google.maps.KmlLayer('http://wpxdnvdsde01:6080/arcgis/services/PetraWells/MapServer/KmlServer?Composite=false&LayerIDs=2');
	//ctaLayer.setMap(map);
	var url = 'http://wpxdnvdsde01:6080/arcgis/rest/services/PetraWells/MapServer';
	var dynamap = new gmaps.ags.MapOverlay(url);//, { opacity: 0.5 });
	dynamap.setMap(map)

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: pointArray,
	//dissipating: false,
	radius: 10,
	opacity: 0.8,
	maxIntensity: 12,
  });

  heatmap.setMap(map);
}

function toggleHeatmap() {
  heatmap.setMap(heatmap.getMap() ? null : map);
}

function changeGradient() {
  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ]
  heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
}

function changeRadius() {
  heatmap.set('radius', heatmap.get('radius') ? null : 30);
}

function changeOpacity() {
  heatmap.set('opacity', heatmap.get('opacity') ? null : 0.95);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>

  <body>
    <div id="panel">
      <button onclick="toggleHeatmap()">Toggle Heatmap</button>
      <button onclick="changeGradient()">Change gradient</button>
      <button onclick="changeRadius()">Change radius</button>
      <button onclick="changeOpacity()">Change opacity</button>
    </div>
    <div id="map-canvas"></div>
  </body>
</html>
