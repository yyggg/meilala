<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<script type="text/javascript">
	$.getJSON("../plus/getIP.php",function(json){
		var myprovince2 = json.data.region.replace('省','');		
		var mycity2 = json.data.city;
		//$("#city_2").html("您所在的城市是："+myprovince2+mycity2);
		$("#city_2").html(myprovince2);
	});
</script>

<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=v1VHB2SMATtGZ3OvHTUm43mA"></script> 
<script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script> 
<script> 
$(function(){ 
navigator.geolocation.getCurrentPosition(translatePoint); //定位 
}); 
function translatePoint(position){ 
var currentLat = position.coords.latitude; 
var currentLon = position.coords.longitude; 
var gpsPoint = new BMap.Point(currentLon, currentLat); 
BMap.Convertor.translate(gpsPoint, 0, initMap); //转换坐标 
} 
function initMap(point){ 
//初始化地图 
map = new BMap.Map("map"); 
map.addControl(new BMap.NavigationControl()); 
map.addControl(new BMap.ScaleControl()); 
map.addControl(new BMap.OverviewMapControl()); 
map.centerAndZoom(point, 15); 
map.addOverlay(new BMap.Marker(point)) 
} 
</script> 
<div id="map"></div> 
